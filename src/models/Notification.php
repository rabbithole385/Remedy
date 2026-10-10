<?php
// ============================================================
// Remedy Investment — Notification Model
// ============================================================

require_once __DIR__ . '/../config/db.php';

class Notification {
    public static function create(int $userId, string $title, string $message, string $type = 'info', ?string $link = null): bool {
        $sql = "INSERT INTO notifications (user_id, title, message, type, link) VALUES (?, ?, ?, ?, ?)";
        return DB::execute($sql, [$userId, $title, $message, $type, $link]) > 0;
    }

    public static function getByUser(int $userId, int $limit = 20): array {
        $sql = "SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT ?";
        return DB::fetchAll($sql, [$userId, $limit]);
    }

    public static function getUnreadCount(int $userId): int {
        $res = DB::fetch("SELECT COUNT(*) as cnt FROM notifications WHERE user_id = ? AND is_read = 0", [$userId]);
        return (int)($res['cnt'] ?? 0);
    }

    public static function markAllRead(int $userId): bool {
        return DB::execute("UPDATE notifications SET is_read = 1 WHERE user_id = ?", [$userId]) > 0;
    }

    public static function markRead(int $id, int $userId): bool {
        return DB::execute("UPDATE notifications SET is_read = 1 WHERE id = ? AND user_id = ?", [$id, $userId]) > 0;
    }
}
