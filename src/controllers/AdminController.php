<?php
// ============================================================
// Remedy Investment — Admin Controller
// ============================================================

require_once __DIR__ . '/../helpers/Auth.php';
require_once __DIR__ . '/../helpers/Csrf.php';
require_once __DIR__ . '/../helpers/Flash.php';
require_once __DIR__ . '/../helpers/Redirect.php';
require_once __DIR__ . '/../helpers/Crypto.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Task.php';
require_once __DIR__ . '/../models/Transaction.php';
require_once __DIR__ . '/../models/Referral.php';
require_once __DIR__ . '/../models/Setting.php';
require_once __DIR__ . '/../models/Notification.php';

class AdminController {
    public function __construct() {
        Auth::requireAdmin();
    }

    public function index(): void {
        $totalUsers = User::countAll();
        
        $resDep = DB::fetch("SELECT COALESCE(SUM(amount), 0) as total FROM transactions WHERE type = 'deposit' AND status = 'approved'");
        $totalDeposits = (float)($resDep['total'] ?? 0);

        $resWth = DB::fetch("SELECT COALESCE(SUM(amount), 0) as total FROM transactions WHERE type = 'withdrawal' AND status = 'approved'");
        $totalWithdrawals = (float)($resWth['total'] ?? 0);

        $resPendingDep = DB::fetch("SELECT COUNT(*) as cnt FROM transactions WHERE type = 'deposit' AND status = 'pending'");
        $pendingDepositsCount = (int)($resPendingDep['cnt'] ?? 0);

        $resPendingWth = DB::fetch("SELECT COUNT(*) as cnt FROM transactions WHERE type = 'withdrawal' AND status = 'pending'");
        $pendingWithdrawalsCount = (int)($resPendingWth['cnt'] ?? 0);

        $resPendingSub = DB::fetch("SELECT COUNT(*) as cnt FROM task_submissions WHERE status = 'pending'");
        $pendingSubmissionsCount = (int)($resPendingSub['cnt'] ?? 0);

        $recentTransactions = Transaction::getAllAdmin(null, null, 8);
        $recentUsers = User::getAll(6);

        require_once __DIR__ . '/../../views/admin/index.php';
    }

    public function users(): void {
        $search = $_GET['q'] ?? null;
        $users = User::getAll(100, 0, $search);
        require_once __DIR__ . '/../../views/admin/users.php';
    }

    public function toggleUserStatus(): void {
        if (!Csrf::verify($_POST['csrf_token'] ?? '')) {
            Flash::error('Invalid token.');
            Redirect::to('admin/users');
        }

        $userId = (int)($_POST['user_id'] ?? 0);
        $user = User::findById($userId);
        if ($user && $user['role'] !== 'admin') {
            $newStatus = ($user['status'] === 'active') ? 'banned' : 'active';
            DB::execute("UPDATE users SET status = ? WHERE id = ?", [$newStatus, $userId]);
            Flash::success("User {$user['username']} status set to {$newStatus}.");
        }
        Redirect::to('admin/users');
    }

    public function adjustUserBalance(): void {
        if (!Csrf::verify($_POST['csrf_token'] ?? '')) {
            Flash::error('Invalid token.');
            Redirect::to('admin/users');
        }

        $userId = (int)($_POST['user_id'] ?? 0);
        $type = $_POST['action_type'] ?? 'credit'; // credit or debit
        $amount = (float)($_POST['amount'] ?? 0);
        $note = trim($_POST['note'] ?? 'Admin manual adjustment');

        $user = User::findById($userId);
        if (!$user || $amount <= 0) {
            Flash::error('Invalid user or amount.');
            Redirect::to('admin/users');
        }

        $balanceBefore = (float)$user['balance'];
        if ($type === 'credit') {
            $balanceAfter = $balanceBefore + $amount;
            User::updateBalance($userId, $amount, 'add');
            $txType = 'manual_credit';
        } else {
            if ($amount > $balanceBefore) {
                Flash::error('Cannot debit more than user current balance.');
                Redirect::to('admin/users');
            }
            $balanceAfter = $balanceBefore - $amount;
            User::updateBalance($userId, $amount, 'subtract');
            $txType = 'manual_debit';
        }

        Transaction::create([
            'user_id'        => $userId,
            'type'           => $txType,
            'amount'         => $amount,
            'net_amount'     => $amount,
            'balance_before' => $balanceBefore,
            'balance_after'  => $balanceAfter,
            'status'         => 'approved',
            'note'           => $note,
            'reviewed_by'    => Auth::id(),
            'reviewed_at'    => date('Y-m-d H:i:s'),
        ]);

        Notification::create(
            $userId,
            'Balance Adjustment',
            "Your account was " . ($type === 'credit' ? 'credited' : 'debited') . " with " . Crypto::formatMoney($amount) . ". Note: {$note}",
            $type === 'credit' ? 'success' : 'warning',
            'wallet'
        );

        Flash::success("Balance successfully updated for {$user['username']}.");
        Redirect::to('admin/users');
    }

    public function tasks(): void {
        $tasks = Task::getAllAdmin();
        require_once __DIR__ . '/../../views/admin/tasks.php';
    }

    public function saveTask(): void {
        if (!Csrf::verify($_POST['csrf_token'] ?? '')) {
            Flash::error('Invalid token.');
            Redirect::to('admin/tasks');
        }

        $id = (int)($_POST['id'] ?? 0);
        $data = [
            'title'           => trim($_POST['title'] ?? ''),
            'description'     => trim($_POST['description'] ?? ''),
            'instructions'    => trim($_POST['instructions'] ?? ''),
            'type'            => $_POST['type'] ?? 'other',
            'reward'          => (float)($_POST['reward'] ?? 0),
            'target_url'      => trim($_POST['target_url'] ?? ''),
            'max_submissions' => !empty($_POST['max_submissions']) ? (int)$_POST['max_submissions'] : null,
            'status'          => $_POST['status'] ?? 'active',
            'created_by'      => Auth::id(),
        ];

        if (empty($data['title']) || empty($data['description'])) {
            Flash::error('Please fill in task title and description.');
            Redirect::to('admin/tasks');
        }

        // Thumbnail handle
        if (!empty($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['thumbnail'];
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = 'task_thumb_' . time() . '_' . bin2hex(random_bytes(3)) . '.' . $ext;
            if (!is_dir(UPLOAD_DIR)) mkdir(UPLOAD_DIR, 0755, true);
            if (move_uploaded_file($file['tmp_name'], UPLOAD_DIR . $filename)) {
                $data['thumbnail'] = 'uploads/' . $filename;
            }
        }

        if ($id > 0) {
            Task::update($id, $data);
            Flash::success('Task updated successfully.');
        } else {
            Task::create($data);
            Flash::success('New task created successfully.');
        }

        Redirect::to('admin/tasks');
    }

    public function deleteTask(): void {
        if (!Csrf::verify($_POST['csrf_token'] ?? '')) {
            Flash::error('Invalid token.');
            Redirect::to('admin/tasks');
        }

        $id = (int)($_POST['id'] ?? 0);
        Task::delete($id);
        Flash::success('Task removed.');
        Redirect::to('admin/tasks');
    }

    public function submissions(): void {
        $statusFilter = $_GET['status'] ?? null;
        $submissions = Task::getAllSubmissions($statusFilter);
        require_once __DIR__ . '/../../views/admin/submissions.php';
    }

    public function processSubmission(): void {
        if (!Csrf::verify($_POST['csrf_token'] ?? '')) {
            Flash::error('Invalid token.');
            Redirect::to('admin/submissions');
        }

        $id = (int)($_POST['submission_id'] ?? 0);
        $action = $_POST['action'] ?? ''; // approve or reject
        $reason = trim($_POST['rejection_reason'] ?? '');

        $submission = Task::getSubmissionById($id);
        if (!$submission || $submission['status'] !== 'pending') {
            Flash::error('Submission is not pending or does not exist.');
            Redirect::to('admin/submissions');
        }

        $adminId = Auth::id();
        $userId = $submission['user_id'];
        $reward = (float)$submission['task_reward'];

        if ($action === 'approve') {
            $user = User::findById($userId);
            $balanceBefore = (float)$user['balance'];
            $balanceAfter = $balanceBefore + $reward;

            User::updateBalance($userId, $reward, 'add');

            DB::execute(
                "UPDATE task_submissions SET status = 'approved', reviewed_by = ?, reviewed_at = NOW(), reward_credited = ? WHERE id = ?",
                [$adminId, $reward, $id]
            );

            Transaction::create([
                'user_id'        => $userId,
                'type'           => 'task_reward',
                'amount'         => $reward,
                'net_amount'     => $reward,
                'balance_before' => $balanceBefore,
                'balance_after'  => $balanceAfter,
                'status'         => 'approved',
                'note'           => "Reward for completing: {$submission['task_title']}",
                'reviewed_by'    => $adminId,
                'reviewed_at'    => date('Y-m-d H:i:s'),
            ]);

            Notification::create(
                $userId,
                'Task Approved!',
                "Your submission for '{$submission['task_title']}' was approved. " . Crypto::formatMoney($reward) . " was credited to your balance.",
                'success',
                'tasks'
            );

            Flash::success("Submission approved and " . Crypto::formatMoney($reward) . " credited to user.");
        } else {
            DB::execute(
                "UPDATE task_submissions SET status = 'rejected', reviewed_by = ?, reviewed_at = NOW(), rejection_reason = ? WHERE id = ?",
                [$adminId, $reason ?: 'Does not meet task requirements', $id]
            );

            Notification::create(
                $userId,
                'Task Submission Rejected',
                "Your submission for '{$submission['task_title']}' was rejected. Reason: " . ($reason ?: 'Proof requirements not met'),
                'error',
                'tasks'
            );

            Flash::info("Submission rejected.");
        }

        Redirect::to('admin/submissions');
    }

    public function deposits(): void {
        $deposits = Transaction::getAllAdmin('deposit', null, 100);
        require_once __DIR__ . '/../../views/admin/deposits.php';
    }

    public function processDeposit(): void {
        if (!Csrf::verify($_POST['csrf_token'] ?? '')) {
            Flash::error('Invalid token.');
            Redirect::to('admin/deposits');
        }

        $txId = (int)($_POST['tx_id'] ?? 0);
        $action = $_POST['action'] ?? ''; // approve or reject

        $tx = Transaction::findById($txId);
        if (!$tx || $tx['type'] !== 'deposit' || $tx['status'] !== 'pending') {
            Flash::error('Invalid or already processed deposit.');
            Redirect::to('admin/deposits');
        }

        $adminId = Auth::id();
        $user = User::findById($tx['user_id']);
        $amount = (float)$tx['amount'];

        if ($action === 'approve') {
            $balanceBefore = (float)$user['balance'];
            $balanceAfter = $balanceBefore + $amount;

            User::updateBalance($user['id'], $amount, 'add');

            DB::execute(
                "UPDATE transactions SET status = 'approved', balance_before = ?, balance_after = ?, reviewed_by = ?, reviewed_at = NOW() WHERE id = ?",
                [$balanceBefore, $balanceAfter, $adminId, $txId]
            );

            // Check if this user was referred and calculate referral commission on first deposit
            if (!empty($user['referred_by'])) {
                $priorApprovedDeposits = DB::fetch(
                    "SELECT COUNT(*) as cnt FROM transactions WHERE user_id = ? AND type = 'deposit' AND status = 'approved' AND id != ?",
                    [$user['id'], $txId]
                );
                if ((int)($priorApprovedDeposits['cnt'] ?? 0) === 0) {
                    $bonusPercent = (float)Setting::get('referral_bonus_percent', '5.00');
                    $bonusAmount = round(($amount * $bonusPercent) / 100, 2);
                    if ($bonusAmount > 0) {
                        $referrer = User::findById($user['referred_by']);
                        if ($referrer) {
                            $refBefore = (float)$referrer['balance'];
                            $refAfter = $refBefore + $bonusAmount;
                            User::updateBalance($referrer['id'], $bonusAmount, 'add');

                            DB::execute(
                                "UPDATE referrals SET bonus_amount = ?, bonus_paid = 1, bonus_paid_at = NOW() WHERE referrer_id = ? AND referee_id = ?",
                                [$bonusAmount, $referrer['id'], $user['id']]
                            );

                            Transaction::create([
                                'user_id'        => $referrer['id'],
                                'type'           => 'referral_bonus',
                                'amount'         => $bonusAmount,
                                'net_amount'     => $bonusAmount,
                                'balance_before' => $refBefore,
                                'balance_after'  => $refAfter,
                                'status'         => 'approved',
                                'note'           => "Referral commission from {$user['username']}'s first deposit",
                                'reviewed_by'    => $adminId,
                                'reviewed_at'    => date('Y-m-d H:i:s'),
                            ]);

                            Notification::create(
                                $referrer['id'],
                                'Referral Commission Received!',
                                "You received " . Crypto::formatMoney($bonusAmount) . " commission from {$user['username']}'s initial deposit.",
                                'success',
                                'referral'
                            );
                        }
                    }
                }
            }

            Notification::create(
                $user['id'],
                'Deposit Approved',
                "Your deposit of " . Crypto::formatMoney($amount) . " has been verified and added to your balance.",
                'success',
                'wallet'
            );

            Flash::success("Deposit of " . Crypto::formatMoney($amount) . " successfully approved.");
        } else {
            DB::execute("UPDATE transactions SET status = 'rejected', reviewed_by = ?, reviewed_at = NOW() WHERE id = ?", [$adminId, $txId]);

            Notification::create(
                $user['id'],
                'Deposit Rejected',
                "Your deposit submission for " . Crypto::formatMoney($amount) . " could not be verified.",
                'error',
                'wallet'
            );

            Flash::info("Deposit marked as rejected.");
        }

        Redirect::to('admin/deposits');
    }

    public function withdrawals(): void {
        $withdrawals = Transaction::getAllAdmin('withdrawal', null, 100);
        require_once __DIR__ . '/../../views/admin/withdrawals.php';
    }

    public function processWithdrawal(): void {
        if (!Csrf::verify($_POST['csrf_token'] ?? '')) {
            Flash::error('Invalid token.');
            Redirect::to('admin/withdrawals');
        }

        $txId = (int)($_POST['tx_id'] ?? 0);
        $action = $_POST['action'] ?? ''; // approve or reject
        $txHash = trim($_POST['tx_hash'] ?? '');

        $tx = Transaction::findById($txId);
        if (!$tx || $tx['type'] !== 'withdrawal' || $tx['status'] !== 'pending') {
            Flash::error('Invalid or already processed withdrawal.');
            Redirect::to('admin/withdrawals');
        }

        $adminId = Auth::id();
        $user = User::findById($tx['user_id']);

        if ($action === 'approve') {
            DB::execute(
                "UPDATE transactions SET status = 'approved', tx_hash = ?, reviewed_by = ?, reviewed_at = NOW() WHERE id = ?",
                [$txHash ?: null, $adminId, $txId]
            );

            Notification::create(
                $user['id'],
                'Withdrawal Completed',
                "Your withdrawal of " . Crypto::formatMoney($tx['net_amount']) . " has been sent to your wallet (" . Crypto::truncate($tx['crypto_address']) . ").",
                'success',
                'transactions'
            );

            Flash::success("Withdrawal approved and marked complete.");
        } else {
            // Refund user balance
            $refundAmount = (float)$tx['amount'];
            User::updateBalance($user['id'], $refundAmount, 'add');

            DB::execute("UPDATE transactions SET status = 'rejected', reviewed_by = ?, reviewed_at = NOW() WHERE id = ?", [$adminId, $txId]);

            Notification::create(
                $user['id'],
                'Withdrawal Rejected & Refunded',
                "Your withdrawal request of " . Crypto::formatMoney($refundAmount) . " was rejected and refunded to your balance.",
                'error',
                'wallet'
            );

            Flash::info("Withdrawal rejected and balance refunded to user.");
        }

        Redirect::to('admin/withdrawals');
    }

    public function transactions(): void {
        $type = $_GET['type'] ?? null;
        $status = $_GET['status'] ?? null;
        $transactions = Transaction::getAllAdmin($type, $status, 150);
        require_once __DIR__ . '/../../views/admin/transactions.php';
    }

    public function settings(): void {
        $settings = Setting::getAll();
        require_once __DIR__ . '/../../views/admin/settings.php';
    }

    public function saveSettings(): void {
        if (!Csrf::verify($_POST['csrf_token'] ?? '')) {
            Flash::error('Invalid token.');
            Redirect::to('admin/settings');
        }

        $allowedKeys = [
            'site_name', 'site_email', 'usdt_trc20_wallet', 'usdt_erc20_wallet',
            'btc_wallet', 'min_deposit', 'min_withdrawal', 'referral_bonus_percent',
            'withdrawal_fee_percent', 'site_status', 'maintenance_message'
        ];

        foreach ($allowedKeys as $key) {
            if (isset($_POST[$key])) {
                Setting::set($key, trim($_POST[$key]));
            }
        }

        Flash::success('Settings updated successfully.');
        Redirect::to('admin/settings');
    }
}
