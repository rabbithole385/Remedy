<?php
$pageTitle = "My Wallet";
$currentRoute = "wallet";
ob_start();
?>

<!-- Header -->
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
    <div>
        <h1 class="text-2xl font-black text-white tracking-tight">Crypto & Account Wallet</h1>
        <p class="text-sm text-zinc-400 mt-1">Manage deposits, withdrawal requests and review account balances.</p>
    </div>
    <div class="flex items-center gap-3">
        <a href="deposit" class="btn-brand px-4 py-2 rounded-xl text-xs font-semibold text-white flex items-center gap-2">
            <i data-lucide="arrow-down-left" class="w-4 h-4"></i>
            Deposit
        </a>
        <a href="withdraw" class="px-4 py-2 rounded-xl text-xs font-semibold text-zinc-200 bg-white/5 hover:bg-white/10 border border-white/10 transition-colors flex items-center gap-2">
            <i data-lucide="arrow-up-right" class="w-4 h-4"></i>
            Withdraw
        </a>
    </div>
</div>

<!-- Main Balance Card & Breakdown -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <!-- Big Balance Card -->
    <div class="stat-card rounded-3xl p-6 sm:p-8 bg-gradient-to-br from-sky-900/40 via-zinc-900/80 to-zinc-950 border border-sky-500/20 relative overflow-hidden flex flex-col justify-between">
        <div class="relative z-10">
            <div class="flex items-center justify-between mb-4">
                <span class="text-xs font-bold uppercase tracking-wider text-sky-400">Total Available Balance</span>
                <span class="w-3 h-3 rounded-full bg-emerald-400 animate-pulse"></span>
            </div>
            <div class="text-3xl sm:text-4xl font-black text-white tracking-tight mb-2">
                <?= Crypto::formatMoney($balance) ?>
            </div>
            <p class="text-xs text-zinc-400">Secured with high-grade multi-asset wallet storage</p>
        </div>

        <div class="relative z-10 mt-8 pt-6 border-t border-white/10 flex items-center justify-between">
            <div>
                <p class="text-[11px] text-zinc-400">Account Currency</p>
                <p class="text-xs font-bold text-white">USD (Crypto Pegged)</p>
            </div>
            <div>
                <p class="text-[11px] text-zinc-400">Status</p>
                <p class="text-xs font-bold text-emerald-400">Verified & Active</p>
            </div>
        </div>
    </div>

    <!-- Stats Breakdown -->
    <div class="lg:col-span-2 grid grid-cols-1 sm:grid-cols-3 gap-4">
        <!-- Deposited -->
        <div class="stat-card rounded-2xl p-6 flex flex-col justify-between">
            <div>
                <div class="w-10 h-10 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400 mb-4">
                    <i data-lucide="arrow-down-left" class="w-5 h-5"></i>
                </div>
                <span class="text-xs font-medium text-zinc-400 uppercase tracking-wider">Total Deposited</span>
                <div class="text-xl font-black text-white mt-1"><?= Crypto::formatMoney($totalDeposited) ?></div>
            </div>
            <a href="deposit" class="text-xs text-emerald-400 hover:underline mt-4 inline-flex items-center gap-1">Add funds &rarr;</a>
        </div>

        <!-- Withdrawn -->
        <div class="stat-card rounded-2xl p-6 flex flex-col justify-between">
            <div>
                <div class="w-10 h-10 rounded-xl bg-rose-500/10 border border-rose-500/20 flex items-center justify-center text-rose-400 mb-4">
                    <i data-lucide="arrow-up-right" class="w-5 h-5"></i>
                </div>
                <span class="text-xs font-medium text-zinc-400 uppercase tracking-wider">Total Withdrawn</span>
                <div class="text-xl font-black text-white mt-1"><?= Crypto::formatMoney($totalWithdrawn) ?></div>
            </div>
            <a href="withdraw" class="text-xs text-rose-400 hover:underline mt-4 inline-flex items-center gap-1">Cash out &rarr;</a>
        </div>

        <!-- Earned -->
        <div class="stat-card rounded-2xl p-6 flex flex-col justify-between">
            <div>
                <div class="w-10 h-10 rounded-xl bg-sky-500/10 border border-sky-500/20 flex items-center justify-center text-sky-400 mb-4">
                    <i data-lucide="award" class="w-5 h-5"></i>
                </div>
                <span class="text-xs font-medium text-zinc-400 uppercase tracking-wider">Task Rewards</span>
                <div class="text-xl font-black text-white mt-1"><?= Crypto::formatMoney($totalEarned) ?></div>
            </div>
            <a href="tasks" class="text-xs text-sky-400 hover:underline mt-4 inline-flex items-center gap-1">Earn more &rarr;</a>
        </div>
    </div>
</div>

<!-- Recent Wallet Activity -->
<div class="stat-card rounded-2xl p-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h3 class="text-base font-bold text-white">Wallet History</h3>
            <p class="text-xs text-zinc-400 mt-0.5">Recent credits and debits</p>
        </div>
        <a href="transactions" class="text-xs text-sky-400 hover:text-sky-300 font-medium">View All Transactions &rarr;</a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs">
            <thead>
                <tr class="text-zinc-400 uppercase tracking-wider border-b border-white/5 pb-3">
                    <th class="pb-3 font-semibold">Reference</th>
                    <th class="pb-3 font-semibold">Type</th>
                    <th class="pb-3 font-semibold">Amount</th>
                    <th class="pb-3 font-semibold">Status</th>
                    <th class="pb-3 font-semibold">Date</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                <?php if (empty($recentTxs)): ?>
                    <tr>
                        <td colspan="5" class="py-6 text-center text-zinc-500">No transactions recorded yet.</td>
                    </tr>
                <?php else: foreach ($recentTxs as $tx): ?>
                    <tr class="hover:bg-white/[0.02] transition-colors">
                        <td class="py-3 font-mono text-zinc-300"><?= htmlspecialchars($tx['reference']) ?></td>
                        <td class="py-3">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-medium
                                <?= match($tx['type']) {
                                    'deposit'         => 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20',
                                    'withdrawal'      => 'bg-rose-500/10 text-rose-400 border border-rose-500/20',
                                    'task_reward'     => 'bg-sky-500/10 text-sky-400 border border-sky-500/20',
                                    'referral_bonus'  => 'bg-purple-500/10 text-purple-400 border border-purple-500/20',
                                    default           => 'bg-zinc-500/10 text-zinc-400 border border-zinc-500/20',
                                } ?>">
                                <?= ucwords(str_replace('_', ' ', $tx['type'])) ?>
                            </span>
                        </td>
                        <td class="py-3 font-bold <?= in_array($tx['type'], ['deposit', 'task_reward', 'referral_bonus', 'manual_credit']) ? 'text-emerald-400' : 'text-rose-400' ?>">
                            <?= in_array($tx['type'], ['deposit', 'task_reward', 'referral_bonus', 'manual_credit']) ? '+' : '-' ?><?= Crypto::formatMoney($tx['net_amount']) ?>
                        </td>
                        <td class="py-3">
                            <span class="px-2 py-0.5 rounded text-[10px] uppercase font-bold
                                <?= match($tx['status']) {
                                    'approved' => 'bg-emerald-500/20 text-emerald-300',
                                    'pending'  => 'bg-amber-500/20 text-amber-300',
                                    'rejected' => 'bg-rose-500/20 text-rose-300',
                                    default    => 'bg-zinc-500/20 text-zinc-300',
                                } ?>">
                                <?= $tx['status'] ?>
                            </span>
                        </td>
                        <td class="py-3 text-zinc-400"><?= date('M d, Y H:i', strtotime($tx['created_at'])) ?></td>
                    </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
$slot = ob_get_clean();
require __DIR__ . '/../layouts/app.php';
?>
