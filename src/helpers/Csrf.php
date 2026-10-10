<?php
// ============================================================
// Remedy Investment — CSRF Protection Helper
// ============================================================

class Csrf {
    public static function token(): string {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (empty($_SESSION[CSRF_TOKEN_KEY])) {
            $_SESSION[CSRF_TOKEN_KEY] = bin2hex(random_bytes(32));
        }
        return $_SESSION[CSRF_TOKEN_KEY];
    }

    public static function field(): string {
        $token = self::token();
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
    }

    public static function verify(?string $token): bool {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (empty($_SESSION[CSRF_TOKEN_KEY]) || empty($token)) {
            return false;
        }
        return hash_equals($_SESSION[CSRF_TOKEN_KEY], $token);
    }
}
