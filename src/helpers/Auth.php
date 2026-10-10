<?php
// ============================================================
// Remedy Investment — Authentication & Session Helper
// ============================================================

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/Redirect.php';
require_once __DIR__ . '/Flash.php';

class Auth {
    public static function init(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_name(SESSION_NAME);
            ini_set('session.cookie_httponly', '1');
            ini_set('session.use_only_cookies', '1');
            ini_set('session.cookie_lifetime', SESSION_LIFETIME);
            session_start();
        }
    }

    public static function check(): bool {
        self::init();
        return !empty($_SESSION['user_id']);
    }

    public static function user(): ?array {
        self::init();
        if (!self::check()) {
            return null;
        }

        $userId = $_SESSION['user_id'];
        $user = DB::fetch("SELECT * FROM users WHERE id = ?", [$userId]);
        if (!$user) {
            // User was deleted or invalidated
            self::logout();
            return null;
        }
        return $user;
    }

    public static function id(): ?int {
        self::init();
        return $_SESSION['user_id'] ?? null;
    }

    public static function role(): ?string {
        self::init();
        return $_SESSION['user_role'] ?? null;
    }

    public static function isAdmin(): bool {
        return self::role() === 'admin';
    }

    public static function login(array $user): void {
        self::init();
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['user_name'] = $user['username'];
        $_SESSION['user_email'] = $user['email'];

        // Update last login
        DB::execute("UPDATE users SET last_login_at = NOW() WHERE id = ?", [$user['id']]);
    }

    public static function logout(): void {
        self::init();
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
    }

    public static function requireAuth(): void {
        self::init();
        if (!self::check()) {
            Flash::error("Please sign in to access that page.");
            Redirect::to('login');
        }

        // Verify not banned
        $user = self::user();
        if ($user && $user['status'] === 'banned') {
            self::logout();
            Flash::error("Your account has been suspended. Please contact support.");
            Redirect::to('login');
        }
    }

    public static function requireAdmin(): void {
        self::requireAuth();
        if (!self::isAdmin()) {
            Flash::error("Access denied. Administrator privileges required.");
            Redirect::to('dashboard');
        }
    }

    public static function requireGuest(): void {
        self::init();
        if (self::check()) {
            if (self::isAdmin()) {
                Redirect::to('admin');
            } else {
                Redirect::to('dashboard');
            }
        }
    }
}
