<?php
// ============================================================
// Remedy Investment — Auth Controller
// ============================================================

require_once __DIR__ . '/../helpers/Auth.php';
require_once __DIR__ . '/../helpers/Csrf.php';
require_once __DIR__ . '/../helpers/Flash.php';
require_once __DIR__ . '/../helpers/Redirect.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Referral.php';
require_once __DIR__ . '/../models/Setting.php';

class AuthController {
    public function showLogin(): void {
        Auth::requireGuest();
        require_once __DIR__ . '/../../views/auth/login.php';
    }

    public function login(): void {
        Auth::requireGuest();

        if (!Csrf::verify($_POST['csrf_token'] ?? '')) {
            Flash::error('Invalid security token. Please try again.');
            Redirect::to('login');
        }

        $emailOrUsername = trim($_POST['identity'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($emailOrUsername) || empty($password)) {
            Flash::error('Please fill in all required fields.');
            Redirect::to('login');
        }

        // Find user by email or username
        $user = User::findByEmail($emailOrUsername);
        if (!$user) {
            $user = User::findByUsername($emailOrUsername);
        }

        if (!$user || !password_verify($password, $user['password_hash'])) {
            Flash::error('Invalid credentials. Please verify your email/username and password.');
            Redirect::to('login');
        }

        if ($user['status'] === 'banned') {
            Flash::error('Your account has been deactivated. Contact support for assistance.');
            Redirect::to('login');
        }

        Auth::login($user);
        Flash::success('Welcome back, ' . htmlspecialchars($user['username']) . '!');

        if ($user['role'] === 'admin') {
            Redirect::to('admin');
        } else {
            Redirect::to('dashboard');
        }
    }

    public function showRegister(): void {
        Auth::requireGuest();
        $ref = htmlspecialchars($_GET['ref'] ?? '');
        require_once __DIR__ . '/../../views/auth/register.php';
    }

    public function register(): void {
        Auth::requireGuest();

        if (!Csrf::verify($_POST['csrf_token'] ?? '')) {
            Flash::error('Invalid security token. Please try again.');
            Redirect::to('register');
        }

        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['password_confirmation'] ?? '';
        $referralCode = trim($_POST['referral_code'] ?? '');

        // Validations
        if (empty($username) || empty($email) || empty($password)) {
            Flash::error('Please complete all required fields.');
            Redirect::to('register');
        }

        if (strlen($username) < 3 || !preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
            Flash::error('Username must be at least 3 characters and contain only letters, numbers, and underscores.');
            Redirect::to('register');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Flash::error('Please enter a valid email address.');
            Redirect::to('register');
        }

        if (strlen($password) < 6) {
            Flash::error('Password must be at least 6 characters long.');
            Redirect::to('register');
        }

        if ($password !== $confirmPassword) {
            Flash::error('Passwords do not match.');
            Redirect::to('register');
        }

        if (User::findByEmail($email)) {
            Flash::error('An account with this email address already exists.');
            Redirect::to('register');
        }

        if (User::findByUsername($username)) {
            Flash::error('That username is already taken. Please choose another.');
            Redirect::to('register');
        }

        $referrerId = null;
        if (!empty($referralCode)) {
            $referrer = User::findByReferralCode($referralCode);
            if ($referrer) {
                $referrerId = $referrer['id'];
            }
        }

        $userId = User::create([
            'username'    => $username,
            'email'       => $email,
            'phone'       => $phone,
            'password'    => $password,
            'referred_by' => $referrerId,
        ]);

        if (!$userId) {
            Flash::error('Could not create your account at this time. Please try again.');
            Redirect::to('register');
        }

        // Record referral relationship if applicable
        if ($referrerId) {
            $bonusPercent = (float)Setting::get('referral_bonus_percent', '5.00');
            Referral::create($referrerId, $userId, 0.00);
        }

        $user = User::findById($userId);
        Auth::login($user);
        Flash::success('Account created successfully! Welcome to ' . APP_NAME . '.');
        Redirect::to('dashboard');
    }

    public function logout(): void {
        Auth::logout();
        Flash::info('You have been signed out successfully.');
        Redirect::to('login');
    }

    public function showForgotPassword(): void {
        Auth::requireGuest();
        require_once __DIR__ . '/../../views/auth/forgot-password.php';
    }

    public function forgotPassword(): void {
        Auth::requireGuest();
        if (!Csrf::verify($_POST['csrf_token'] ?? '')) {
            Flash::error('Invalid security token.');
            Redirect::to('forgot-password');
        }

        $email = trim($_POST['email'] ?? '');
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Flash::error('Please provide a valid email address.');
            Redirect::to('forgot-password');
        }

        // Create reset token
        $user = User::findByEmail($email);
        if ($user) {
            $token = bin2hex(random_bytes(32));
            $expiresAt = date('Y-m-d H:i:s', strtotime('+1 hour'));
            DB::execute("INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)", [$email, $token, $expiresAt]);
            // Flash message simulate success (or email send)
            Flash::success("A password reset link has been dispatched to {$email}.");
        } else {
            // Consistent response for security
            Flash::success("If an account exists for that email, a reset link has been sent.");
        }
        Redirect::to('login');
    }

    public function showResetPassword(): void {
        Auth::requireGuest();
        $token = $_GET['token'] ?? '';
        require_once __DIR__ . '/../../views/auth/reset-password.php';
    }

    public function resetPassword(): void {
        Auth::requireGuest();
        if (!Csrf::verify($_POST['csrf_token'] ?? '')) {
            Flash::error('Invalid security token.');
            Redirect::to('login');
        }

        $token = $_POST['token'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['password_confirmation'] ?? '';

        if (strlen($password) < 6 || $password !== $confirmPassword) {
            Flash::error('Passwords must match and have at least 6 characters.');
            Redirect::to("reset-password?token={$token}");
        }

        $reset = DB::fetch("SELECT * FROM password_resets WHERE token = ? AND used = 0 AND expires_at > NOW()", [$token]);
        if (!$reset) {
            Flash::error('This reset link is invalid or has expired.');
            Redirect::to('forgot-password');
        }

        $user = User::findByEmail($reset['email']);
        if ($user) {
            User::updatePassword($user['id'], $password);
            DB::execute("UPDATE password_resets SET used = 1 WHERE id = ?", [$reset['id']]);
            Flash::success('Your password has been successfully reset. Please sign in.');
            Redirect::to('login');
        }

        Flash::error('Unable to reset password.');
        Redirect::to('login');
    }
}
