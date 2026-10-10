<?php
// ============================================================
// Remedy Investment — Setting Model
// ============================================================

require_once __DIR__ . '/../config/db.php';

class Setting {
    public static function get(string $key, ?string $default = null): ?string {
        $res = DB::fetch("SELECT value FROM settings WHERE `key` = ?", [$key]);
        return $res ? $res['value'] : $default;
    }

    public static function set(string $key, ?string $value): bool {
        $sql = "INSERT INTO settings (`key`, `value`) VALUES (?, ?)
                ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)";
        return DB::execute($sql, [$key, $value]) >= 0;
    }

    public static function getAll(): array {
        $rows = DB::fetchAll("SELECT `key`, `value` FROM settings");
        $settings = [];
        foreach ($rows as $row) {
            $settings[$row['key']] = $row['value'];
        }
        return $settings;
    }
}
