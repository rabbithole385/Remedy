<?php
// ============================================================
// Remedy Investment — Dashboard Controller
// ============================================================

require_once __DIR__ . '/../helpers/Auth.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Task.php';
require_once __DIR__ . '/../models/Transaction.php';
require_once __DIR__ . '/../models/Referral.php';
require_once __DIR__ . '/../models/Notification.php';

class DashboardController {
    public function index(): void {
        Auth::requireAuth();
        $user = Auth::user();

        // Retrieve dashboard stats
        $balance = (float)$user['balance'];
        $totalEarned = Transaction::getTotalUserEarnings($user['id']);
        $totalDeposited = Transaction::getTotalUserDeposits($user['id']);
        $totalWithdrawn = Transaction::getTotalUserWithdrawals($user['id']);

        $tasks = Task::getActive($user['id']);
        $availableTasksCount = count(array_filter($tasks, fn($t) => empty($t['submission_id'])));
        $completedTasksCount = count(array_filter($tasks, fn($t) => !empty($t['submission_id']) && $t['submission_status'] === 'approved'));

        $referralCount = Referral::getReferralCount($user['id']);
        $referralBonus = Referral::getTotalReferralBonus($user['id']);

        $recentTransactions = Transaction::getByUser($user['id'], 6);
        $recentNotifications = Notification::getByUser($user['id'], 5);
        $unreadNotificationCount = Notification::getUnreadCount($user['id']);

        require_once __DIR__ . '/../../views/dashboard/index.php';
    }
}
