<?php
// ============================================================
// Remedy Investment — Profile & Notification Controller
// ============================================================

require_once __DIR__ . '/../helpers/Auth.php';
require_once __DIR__ . '/../helpers/Csrf.php';
require_once __DIR__ . '/../helpers/Flash.php';
require_once __DIR__ . '/../helpers/Redirect.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Notification.php';

class ProfileController {
    public function index(): void {
        Auth::requireAuth();
        $user = Auth::user();
        require_once __DIR__ . '/../../views/dashboard/profile.php';
    }

    public function updateProfile(): void {
        Auth::requireAuth();
        $user = Auth::user();

        if (!Csrf::verify($_POST['csrf_token'] ?? '')) {
            Flash::error('Invalid security token.');
            Redirect::to('profile');
        }

        $firstName = trim($_POST['first_name'] ?? '');
        $lastName = trim($_POST['last_name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');

        $data = [
            'first_name' => $firstName,
            'last_name'  => $lastName,
            'phone'      => $phone,
        ];

        // Avatar upload check
        if (!empty($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['avatar'];
            if ($file['size'] <= UPLOAD_MAX_SIZE) {
                $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                $filename = 'avatar_' . $user['id'] . '_' . time() . '.' . $ext;

                if (!is_dir(UPLOAD_DIR)) {
                    mkdir(UPLOAD_DIR, 0755, true);
                }

                if (move_uploaded_file($file['tmp_name'], UPLOAD_DIR . $filename)) {
                    $data['avatar'] = 'uploads/' . $filename;
                }
            }
        }

        User::updateProfile($user['id'], $data);
        Flash::success('Profile details updated successfully.');
        Redirect::to('profile');
    }

    public function updatePassword(): void {
        Auth::requireAuth();
        $user = Auth::user();

        if (!Csrf::verify($_POST['csrf_token'] ?? '')) {
            Flash::error('Invalid security token.');
            Redirect::to('profile');
        }

        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['new_password_confirmation'] ?? '';

        if (!password_verify($currentPassword, $user['password_hash'])) {
            Flash::error('Current password is incorrect.');
            Redirect::to('profile');
        }

        if (strlen($newPassword) < 6) {
            Flash::error('New password must be at least 6 characters.');
            Redirect::to('profile');
        }

        if ($newPassword !== $confirmPassword) {
            Flash::error('New passwords do not match.');
            Redirect::to('profile');
        }

        User::updatePassword($user['id'], $newPassword);
        Flash::success('Your password has been changed successfully.');
        Redirect::to('profile');
    }

    public function notifications(): void {
        Auth::requireAuth();
        $user = Auth::user();

        $notifications = Notification::getByUser($user['id'], 50);
        Notification::markAllRead($user['id']);

        require_once __DIR__ . '/../../views/dashboard/notifications.php';
    }
}
