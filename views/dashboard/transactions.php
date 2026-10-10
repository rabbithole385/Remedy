<?php
$pageTitle = "Transactions History";
$currentRoute = "transactions";
ob_start();
?>

<!-- Header -->
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
    <div>
        <h1 class="text-2xl font-black text-white tracking-tight">Financial & Activity Ledger</h1>
        <p class="text-sm text-zinc-400 mt-1">Audit log of all deposits, withdrawals, task rewards, and referral commissions.</p>
    </div>
    <!-- Filter Pills -->
    <div class="flex flex-wrap items-center gap-2">
        <a href="transactions" class="px-3 py-1.5 rounded-xl text-xs font-semibold <?= empty($typeFilter) ? 'bg-sky-500 text-white' : 'bg-white/5 text-zinc-400 hover:text-white' ?> transition-colors">
            All
        </a>
        <a href="transactions?type=deposit" class="px-3 py-1.5 rounded-xl text-xs font-semibold <?= $typeFilter === 'deposit' ? 'bg-emerald-500 text-white' : 'bg-white/5 text-zinc-400 hover:text-white' ?> transition-colors">
            Deposits
        </a>
        <a href="transactions?type=withdrawal" class="px-3 py-1.5 rounded-xl text-xs font-semibold <?= $typeFilter === 'withdrawal' ? 'bg-rose-500 text-white' : 'bg-white/5 text-zinc-400 hover:text-white' ?> transition-colors">
            Withdrawals
        </a>
        <a href="transactions?type=task_reward" class="px-3 py-1.5 rounded-xl text-xs font-semibold <?= $typeFilter === 'task_reward' ? 'bg-sky-500 text-white' : 'bg-white/5 text-zinc-400 hover:text-white' ?> transition-colors">
            Task Rewards
        </a>
        <a href="transactions?type=referral_bonus" class="px-3 py-1.5 rounded-xl text-xs font-semibold <?= $typeFilter === 'referral_bonus' ? 'bg-purple-500 text-white' : 'bg-white/5 text-zinc-400 hover:text-white' ?> transition-colors">
            Commissions
        </a>
    </div>
</div>

<!-- Transactions Table -->
<div class="stat-card rounded-2xl p-6">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs">
            <thead>
                <tr class="text-zinc-400 uppercase tracking-wider border-b border-white/5 pb-3">
                    <th class="pb-3 font-semibold">Reference</th>
                    <th class="pb-3 font-semibold">Type</th>
                    <th class="pb-3 font-semibold">Gross / Fee</th>
                    <th class="pb-3 font-semibold">Net Amount</th>
                    <th class="pb-3 font-semibold">Asset / Details</th>
                    <th class="pb-3 font-semibold">Status</th>
                    <th class="pb-3 font-semibold">Date</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                <?php if (empty($transactions)): ?>
                    <tr>
                        <td colspan="7" class="py-12 text-center text-zinc-500">
                            <i data-lucide="receipt" class="w-10 h-10 mx-auto mb-2 text-zinc-600"></i>
                            No transactions found for this filter.
                        </td>
                    </tr>
                <?php else: foreach ($transactions as $tx): ?>
                    <tr class="hover:bg-white/[0.02] transition-colors">
                        <!-- Reference -->
                        <td class="py-3.5 font-mono text-zinc-300"><?= htmlspecialchars($tx['reference']) ?></td>

                        <!-- Type -->
                        <td class="py-3.5">
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

                        <!-- Gross / Fee -->
                        <td class="py-3.5 text-zinc-400">
                            <?= Crypto::formatMoney($tx['amount']) ?>
                            <?php if ($tx['fee'] > 0): ?>
                                <span class="text-[10px] text-rose-400 block">-<?= Crypto::formatMoney($tx['fee']) ?> fee</span>
                            <?php endif; ?>
                        </td>

                        <!-- Net Amount -->
                        <td class="py-3.5 font-bold <?= in_array($tx['type'], ['deposit', 'task_reward', 'referral_bonus', 'manual_credit']) ? 'text-emerald-400' : 'text-rose-400' ?>">
                            <?= in_array($tx['type'], ['deposit', 'task_reward', 'referral_bonus', 'manual_credit']) ? '+' : '-' ?><?= Crypto::formatMoney($tx['net_amount']) ?>
                        </td>

                        <!-- Asset / Details -->
                        <td class="py-3.5 text-zinc-300">
                            <?php if (!empty($tx['crypto_type'])): ?>
                                <span class="font-semibold text-white"><?= htmlspecialchars($tx['crypto_type']) ?></span>
                            <?php else: ?>
                                <span class="text-zinc-500">Internal</span>
                            <?php endif; ?>
                            <?php if (!empty($tx['tx_hash'])): ?>
                                <span class="block text-[10px] text-zinc-500 font-mono"><?= Crypto::truncate($tx['tx_hash']) ?></span>
                            <?php endif; ?>
                        </td>

                        <!-- Status -->
                        <td class="py-3.5">
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

                        <!-- Date -->
                        <td class="py-3.5 text-zinc-400"><?= date('M d, Y H:i', strtotime($tx['created_at'])) ?></td>
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
