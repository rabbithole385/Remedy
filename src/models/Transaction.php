<?php
// ============================================================
// Remedy Investment — Transaction Model
// ============================================================

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../helpers/Crypto.php';

class Transaction {
    public static function create(array $data): int|false {
        $reference = $data['reference'] ?? Crypto::generateReference($data['type']);
        $sql = "INSERT INTO transactions 
                (user_id, type, amount, fee, net_amount, balance_before, balance_after, currency, crypto_type, crypto_address, tx_hash, proof_file, status, reference, note)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = DB::query($sql, [
            $data['user_id'],
            $data['type'],
            $data['amount'],
            $data['fee'] ?? 0.00,
            $data['net_amount'] ?? $data['amount'],
            $data['balance_before'] ?? 0.00,
            $data['balance_after'] ?? 0.00,
            $data['currency'] ?? 'USD',
            $data['crypto_type'] ?? null,
            $data['crypto_address'] ?? null,
            $data['tx_hash'] ?? null,
            $data['proof_file'] ?? null,
            $data['status'] ?? 'pending',
            $reference,
            $data['note'] ?? null,
        ]);

        return $stmt ? (int)DB::lastInsertId() : false;
    }

    public static function findById(int $id): ?array {
        return DB::fetch("SELECT * FROM transactions WHERE id = ?", [$id]);
    }

    public static function findByReference(string $reference): ?array {
        return DB::fetch("SELECT * FROM transactions WHERE reference = ?", [$reference]);
    }

    public static function getByUser(int $userId, int $limit = 50, ?string $type = null): array {
        if ($type) {
            $sql = "SELECT * FROM transactions WHERE user_id = ? AND type = ? ORDER BY created_at DESC LIMIT ?";
            return DB::fetchAll($sql, [$userId, $type, $limit]);
        }
        $sql = "SELECT * FROM transactions WHERE user_id = ? ORDER BY created_at DESC LIMIT ?";
        return DB::fetchAll($sql, [$userId, $limit]);
    }

    public static function getAllAdmin(?string $type = null, ?string $status = null, int $limit = 100): array {
        $where = [];
        $params = [];

        if ($type) {
            $where[] = "t.type = ?";
            $params[] = $type;
        }
        if ($status) {
            $where[] = "t.status = ?";
            $params[] = $status;
        }

        $whereClause = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";
        $params[] = $limit;

        $sql = "SELECT t.*, u.username, u.email, u.balance as current_balance
                FROM transactions t
                JOIN users u ON t.user_id = u.id
                {$whereClause}
                ORDER BY t.created_at DESC
                LIMIT ?";

        return DB::fetchAll($sql, $params);
    }

    public static function getTotalUserEarnings(int $userId): float {
        $res = DB::fetch(
            "SELECT COALESCE(SUM(amount), 0) as total FROM transactions 
             WHERE user_id = ? AND type IN ('task_reward', 'referral_bonus') AND status = 'approved'",
            [$userId]
        );
        return (float)($res['total'] ?? 0);
    }

    public static function getTotalUserDeposits(int $userId): float {
        $res = DB::fetch(
            "SELECT COALESCE(SUM(amount), 0) as total FROM transactions 
             WHERE user_id = ? AND type = 'deposit' AND status = 'approved'",
            [$userId]
        );
        return (float)($res['total'] ?? 0);
    }

    public static function getTotalUserWithdrawals(int $userId): float {
        $res = DB::fetch(
            "SELECT COALESCE(SUM(amount), 0) as total FROM transactions 
             WHERE user_id = ? AND type = 'withdrawal' AND status = 'approved'",
            [$userId]
        );
        return (float)($res['total'] ?? 0);
    }
}
