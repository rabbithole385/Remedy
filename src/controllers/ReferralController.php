<?php
// ============================================================
// Remedy Investment — Referral Controller
// ============================================================

require_once __DIR__ . '/../helpers/Auth.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Referral.php';
require_once __DIR__ . '/../models/Setting.php';

class ReferralController {
    public function index(): void {
        Auth::requireAuth();
        $user = Auth::user();

        $referralCode = $user['referral_code'];
        $referralLink = rtrim(APP_URL, '/') . '/register?ref=' . urlencode($referralCode);
        
        $bonusPercent = (float)Setting::get('referral_bonus_percent', '5.00');
        $referrals = Referral::getReferralsByUser($user['id']);
        $totalBonus = Referral::getTotalReferralBonus($user['id']);
        $totalCount = count($referrals);

        require_once __DIR__ . '/../../views/dashboard/referral.php';
    }
}
