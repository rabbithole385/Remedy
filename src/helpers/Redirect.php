<?php
// ============================================================
// Remedy Investment — Redirect Helper
// ============================================================

class Redirect {
    public static function to(string $path): void {
        // Normalize URL if not absolute
        if (!str_starts_with($path, 'http://') && !str_starts_with($path, 'https://')) {
            $base = rtrim(APP_URL, '/');
            $path = $base . '/' . ltrim($path, '/');
        }
        header("Location: " . $path);
        exit;
    }

    public static function back(): void {
        $referer = $_SERVER['HTTP_REFERER'] ?? APP_URL;
        header("Location: " . $referer);
        exit;
    }
}
