<?php
// ============================================================
// Remedy Investment — Referral Model
// ============================================================

require_once __DIR__ . '/../config/db.php';

class Referral {
    public static function create(int $referrerId, int $refereeId, float $bonusAmount = 0.00): bool {
        $sql = "INSERT INTO referrals (referrer_id, referee_id, bonus_amount, bonus_paid)
                VALUES (?, ?, ?, 0)
                ON DUPLICATE KEY UPDATE bonus_amount = VALUES(bonus_amount)";
        return DB::execute($sql, [$referrerId, $refereeId, $bonusAmount]) > 0;
    }

    public static function getReferralsByUser(int $userId): array {
        $sql = "SELECT r.*, u.username, u.email, u.created_at as joined_at, u.balance as user_balance
                FROM referrals r
                JOIN users u ON r.referee_id = u.id
                WHERE r.referrer_id = ?
                ORDER BY r.created_at DESC";
        return DB::fetchAll($sql, [$userId]);
    }

    public static function getReferralCount(int $userId): int {
        $res = DB::fetch("SELECT COUNT(*) as cnt FROM referrals WHERE referrer_id = ?", [$userId]);
        return (int)($res['cnt'] ?? 0);
    }

    public static function getTotalReferralBonus(int $userId): float {
        $res = DB::fetch(
            "SELECT COALESCE(SUM(bonus_amount), 0) as total FROM referrals 
             WHERE referrer_id = ? AND bonus_paid = 1",
            [$userId]
        );
        return (float)($res['total'] ?? 0);
    }
}
