<?php
// ============================================================
// Remedy Investment — Finance Controller
// ============================================================

require_once __DIR__ . '/../helpers/Auth.php';
require_once __DIR__ . '/../helpers/Csrf.php';
require_once __DIR__ . '/../helpers/Flash.php';
require_once __DIR__ . '/../helpers/Redirect.php';
require_once __DIR__ . '/../helpers/Crypto.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Transaction.php';
require_once __DIR__ . '/../models/Setting.php';
require_once __DIR__ . '/../models/Notification.php';

class FinanceController {
    public function wallet(): void {
        Auth::requireAuth();
        $user = Auth::user();

        $balance = (float)$user['balance'];
        $totalDeposited = Transaction::getTotalUserDeposits($user['id']);
        $totalWithdrawn = Transaction::getTotalUserWithdrawals($user['id']);
        $totalEarned = Transaction::getTotalUserEarnings($user['id']);

        $recentTxs = Transaction::getByUser($user['id'], 10);
        require_once __DIR__ . '/../../views/dashboard/wallet.php';
    }

    public function showDeposit(): void {
        Auth::requireAuth();
        $user = Auth::user();

        $minDeposit = (float)Setting::get('min_deposit', '10.00');
        $wallets = [
            'USDT-TRC20' => Crypto::getWalletAddress('USDT-TRC20'),
            'USDT-ERC20' => Crypto::getWalletAddress('USDT-ERC20'),
            'BTC'        => Crypto::getWalletAddress('BTC'),
        ];

        require_once __DIR__ . '/../../views/dashboard/deposit.php';
    }

    public function submitDeposit(): void {
        Auth::requireAuth();
        $user = Auth::user();

        if (!Csrf::verify($_POST['csrf_token'] ?? '')) {
            Flash::error('Invalid security token.');
            Redirect::to('deposit');
        }

        $cryptoType = trim($_POST['crypto_type'] ?? 'USDT-TRC20');
        $amount = (float)($_POST['amount'] ?? 0);
        $txHash = trim($_POST['tx_hash'] ?? '');
        $minDeposit = (float)Setting::get('min_deposit', '10.00');

        if ($amount < $minDeposit) {
            Flash::error("The minimum deposit amount is " . Crypto::formatMoney($minDeposit) . ".");
            Redirect::to('deposit');
        }

        if (empty($txHash)) {
            Flash::error('Please provide the transaction hash / ID.');
            Redirect::to('deposit');
        }

        // File upload proof
        $proofFile = null;
        if (!empty($_FILES['proof_file']) && $_FILES['proof_file']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['proof_file'];
            if ($file['size'] > UPLOAD_MAX_SIZE) {
                Flash::error('Uploaded receipt exceeds 5MB limit.');
                Redirect::to('deposit');
            }

            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = 'deposit_' . $user['id'] . '_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
            
            if (!is_dir(UPLOAD_DIR)) {
                mkdir(UPLOAD_DIR, 0755, true);
            }

            if (move_uploaded_file($file['tmp_name'], UPLOAD_DIR . $filename)) {
                $proofFile = 'uploads/' . $filename;
            }
        }

        $txId = Transaction::create([
            'user_id'        => $user['id'],
            'type'           => 'deposit',
            'amount'         => $amount,
            'net_amount'     => $amount,
            'balance_before' => $user['balance'],
            'balance_after'  => $user['balance'],
            'currency'       => 'USD',
            'crypto_type'    => $cryptoType,
            'crypto_address' => Crypto::getWalletAddress($cryptoType),
            'tx_hash'        => $txHash,
            'proof_file'     => $proofFile,
            'status'         => 'pending',
            'note'           => "User submitted {$cryptoType} deposit proof",
        ]);

        if ($txId) {
            Notification::create(
                $user['id'],
                'Deposit Request Submitted',
                "Your deposit request of " . Crypto::formatMoney($amount) . " has been submitted for verification.",
                'info',
                'transactions'
            );
            Flash::success('Deposit proof submitted successfully! Your account will be credited as soon as verified.');
            Redirect::to('transactions');
        } else {
            Flash::error('Could not submit deposit. Please try again.');
            Redirect::to('deposit');
        }
    }

    public function showWithdraw(): void {
        Auth::requireAuth();
        $user = Auth::user();

        $balance = (float)$user['balance'];
        $minWithdrawal = (float)Setting::get('min_withdrawal', '5.00');
        $feePercent = (float)Setting::get('withdrawal_fee_percent', '2.00');

        require_once __DIR__ . '/../../views/dashboard/withdraw.php';
    }

    public function submitWithdraw(): void {
        Auth::requireAuth();
        $user = Auth::user();

        if (!Csrf::verify($_POST['csrf_token'] ?? '')) {
            Flash::error('Invalid security token.');
            Redirect::to('withdraw');
        }

        $cryptoType = trim($_POST['crypto_type'] ?? 'USDT-TRC20');
        $cryptoAddress = trim($_POST['crypto_address'] ?? '');
        $amount = (float)($_POST['amount'] ?? 0);
        $minWithdrawal = (float)Setting::get('min_withdrawal', '5.00');
        $feePercent = (float)Setting::get('withdrawal_fee_percent', '2.00');

        if (empty($cryptoAddress)) {
            Flash::error('Please enter your destination wallet address.');
            Redirect::to('withdraw');
        }

        if ($amount < $minWithdrawal) {
            Flash::error("The minimum withdrawal amount is " . Crypto::formatMoney($minWithdrawal) . ".");
            Redirect::to('withdraw');
        }

        if ($amount > (float)$user['balance']) {
            Flash::error('Insufficient balance for this withdrawal request.');
            Redirect::to('withdraw');
        }

        $fee = round(($amount * $feePercent) / 100, 2);
        $netAmount = $amount - $fee;

        // Deduct balance immediately upon request
        $balanceBefore = (float)$user['balance'];
        $balanceAfter = $balanceBefore - $amount;

        $deducted = User::updateBalance($user['id'], $amount, 'subtract');
        if (!$deducted) {
            Flash::error('Could not process withdrawal balance deduction.');
            Redirect::to('withdraw');
        }

        $txId = Transaction::create([
            'user_id'        => $user['id'],
            'type'           => 'withdrawal',
            'amount'         => $amount,
            'fee'            => $fee,
            'net_amount'     => $netAmount,
            'balance_before' => $balanceBefore,
            'balance_after'  => $balanceAfter,
            'currency'       => 'USD',
            'crypto_type'    => $cryptoType,
            'crypto_address' => $cryptoAddress,
            'status'         => 'pending',
            'note'           => "Withdrawal to {$cryptoAddress}",
        ]);

        if ($txId) {
            Notification::create(
                $user['id'],
                'Withdrawal Request Placed',
                "Your withdrawal request of " . Crypto::formatMoney($netAmount) . " ({$cryptoType}) is currently pending admin processing.",
                'info',
                'transactions'
            );
            Flash::success('Withdrawal request placed successfully! Funds will be transferred once processed.');
            Redirect::to('transactions');
        } else {
            // Rollback balance if transaction record failed
            User::updateBalance($user['id'], $amount, 'add');
            Flash::error('Failed to submit withdrawal request.');
            Redirect::to('withdraw');
        }
    }

    public function transactions(): void {
        Auth::requireAuth();
        $user = Auth::user();

        $typeFilter = $_GET['type'] ?? null;
        $transactions = Transaction::getByUser($user['id'], 50, $typeFilter);

        require_once __DIR__ . '/../../views/dashboard/transactions.php';
    }
}
