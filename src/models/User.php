<?php
// ============================================================
// Remedy Investment — User Model
// ============================================================

require_once __DIR__ . '/../config/db.php';

class User {
    public static function findById(int $id): ?array {
        return DB::fetch("SELECT * FROM users WHERE id = ?", [$id]);
    }

    public static function findByEmail(string $email): ?array {
        return DB::fetch("SELECT * FROM users WHERE email = ?", [strtolower(trim($email))]);
    }

    public static function findByUsername(string $username): ?array {
        return DB::fetch("SELECT * FROM users WHERE username = ?", [trim($username)]);
    }

    public static function findByReferralCode(string $code): ?array {
        return DB::fetch("SELECT * FROM users WHERE referral_code = ?", [trim($code)]);
    }

    public static function generateUniqueReferralCode(): string {
        do {
            $code = 'REM' . strtoupper(bin2hex(random_bytes(3)));
            $exists = DB::fetch("SELECT id FROM users WHERE referral_code = ?", [$code]);
        } while ($exists);
        return $code;
    }

    public static function create(array $data): int|false {
        $referralCode = self::generateUniqueReferralCode();
        $passwordHash = password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => BCRYPT_COST]);

        $sql = "INSERT INTO users (username, email, phone, password_hash, first_name, last_name, role, status, balance, referral_code, referred_by)
                VALUES (?, ?, ?, ?, ?, ?, 'user', 'active', 0.00, ?, ?)";

        $stmt = DB::query($sql, [
            trim($data['username']),
            strtolower(trim($data['email'])),
            $data['phone'] ?? null,
            $passwordHash,
            $data['first_name'] ?? '',
            $data['last_name'] ?? '',
            $referralCode,
            $data['referred_by'] ?? null,
        ]);

        if (!$stmt) return false;
        return (int)DB::lastInsertId();
    }

    public static function updateBalance(int $userId, float $amount, string $operation = 'add'): bool {
        if ($operation === 'add') {
            $sql = "UPDATE users SET balance = balance + ? WHERE id = ?";
        } else {
            $sql = "UPDATE users SET balance = balance - ? WHERE id = ? AND balance >= ?";
            return DB::execute($sql, [$amount, $userId, $amount]) > 0;
        }
        return DB::execute($sql, [$amount, $userId]) > 0;
    }

    public static function updateProfile(int $userId, array $data): bool {
        $fields = [];
        $params = [];

        if (isset($data['first_name'])) {
            $fields[] = "first_name = ?";
            $params[] = $data['first_name'];
        }
        if (isset($data['last_name'])) {
            $fields[] = "last_name = ?";
            $params[] = $data['last_name'];
        }
        if (isset($data['phone'])) {
            $fields[] = "phone = ?";
            $params[] = $data['phone'];
        }
        if (isset($data['avatar'])) {
            $fields[] = "avatar = ?";
            $params[] = $data['avatar'];
        }

        if (empty($fields)) return false;

        $params[] = $userId;
        $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = ?";
        return DB::execute($sql, $params) > 0;
    }

    public static function updatePassword(int $userId, string $newPassword): bool {
        $hash = password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => BCRYPT_COST]);
        return DB::execute("UPDATE users SET password_hash = ? WHERE id = ?", [$hash, $userId]) > 0;
    }

    public static function getAll(int $limit = 100, int $offset = 0, ?string $search = null): array {
        if ($search) {
            $term = "%{$search}%";
            return DB::fetchAll(
                "SELECT * FROM users WHERE username LIKE ? OR email LIKE ? OR phone LIKE ? ORDER BY created_at DESC LIMIT ? OFFSET ?",
                [$term, $term, $term, $limit, $offset]
            );
        }
        return DB::fetchAll("SELECT * FROM users ORDER BY created_at DESC LIMIT ? OFFSET ?", [$limit, $offset]);
    }

    public static function countAll(?string $search = null): int {
        if ($search) {
            $term = "%{$search}%";
            $res = DB::fetch(
                "SELECT COUNT(*) as cnt FROM users WHERE username LIKE ? OR email LIKE ? OR phone LIKE ?",
                [$term, $term, $term]
            );
            return (int)($res['cnt'] ?? 0);
        }
        $res = DB::fetch("SELECT COUNT(*) as cnt FROM users");
        return (int)($res['cnt'] ?? 0);
    }
}
