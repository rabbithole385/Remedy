<?php
$pageTitle = "All System Transactions";
$currentRoute = "admin_transactions";
ob_start();
?>

<!-- Header -->
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
    <div>
        <h1 class="text-2xl font-black text-white tracking-tight">System Transactions Log</h1>
        <p class="text-sm text-zinc-400 mt-1">Audit log of all platform financial operations across all accounts.</p>
    </div>
    <div class="flex items-center gap-2">
        <a href="admin/transactions" class="px-3 py-1.5 rounded-xl text-xs font-semibold <?= empty($type) ? 'bg-amber-500 text-black' : 'bg-white/5 text-zinc-400' ?>">All</a>
        <a href="admin/transactions?type=deposit" class="px-3 py-1.5 rounded-xl text-xs font-semibold <?= $type === 'deposit' ? 'bg-amber-500 text-black' : 'bg-white/5 text-zinc-400' ?>">Deposits</a>
        <a href="admin/transactions?type=withdrawal" class="px-3 py-1.5 rounded-xl text-xs font-semibold <?= $type === 'withdrawal' ? 'bg-amber-500 text-black' : 'bg-white/5 text-zinc-400' ?>">Withdrawals</a>
        <a href="admin/transactions?type=task_reward" class="px-3 py-1.5 rounded-xl text-xs font-semibold <?= $type === 'task_reward' ? 'bg-amber-500 text-black' : 'bg-white/5 text-zinc-400' ?>">Tasks</a>
        <a href="admin/transactions?type=referral_bonus" class="px-3 py-1.5 rounded-xl text-xs font-semibold <?= $type === 'referral_bonus' ? 'bg-amber-500 text-black' : 'bg-white/5 text-zinc-400' ?>">Referrals</a>
    </div>
</div>

<div class="stat-card rounded-2xl p-6">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs">
            <thead>
                <tr class="text-zinc-400 uppercase tracking-wider border-b border-white/5 pb-3">
                    <th class="pb-3 font-semibold">Reference</th>
                    <th class="pb-3 font-semibold">User</th>
                    <th class="pb-3 font-semibold">Type</th>
                    <th class="pb-3 font-semibold">Gross / Fee</th>
                    <th class="pb-3 font-semibold">Net Amount</th>
                    <th class="pb-3 font-semibold">Balance Before &rarr; After</th>
                    <th class="pb-3 font-semibold">Status</th>
                    <th class="pb-3 font-semibold">Date</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                <?php if (empty($transactions)): ?>
                    <tr><td colspan="8" class="py-8 text-center text-zinc-500">No transactions recorded.</td></tr>
                <?php else: foreach ($transactions as $tx): ?>
                    <tr class="hover:bg-white/[0.02] transition-colors">
                        <td class="py-3 font-mono text-zinc-300"><?= htmlspecialchars($tx['reference']) ?></td>
                        <td class="py-3 font-bold text-white"><?= htmlspecialchars($tx['username']) ?></td>
                        <td class="py-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-zinc-800 text-zinc-300">
                                <?= str_replace('_', ' ', $tx['type']) ?>
                            </span>
                        </td>
                        <td class="py-3 text-zinc-400">
                            <?= Crypto::formatMoney($tx['amount']) ?>
                            <?php if ($tx['fee'] > 0): ?>
                                <span class="text-[10px] text-rose-400 block">-<?= Crypto::formatMoney($tx['fee']) ?> fee</span>
                            <?php endif; ?>
                        </td>
                        <td class="py-3 font-bold <?= in_array($tx['type'], ['deposit', 'task_reward', 'referral_bonus', 'manual_credit']) ? 'text-emerald-400' : 'text-rose-400' ?>">
                            <?= in_array($tx['type'], ['deposit', 'task_reward', 'referral_bonus', 'manual_credit']) ? '+' : '-' ?><?= Crypto::formatMoney($tx['net_amount']) ?>
                        </td>
                        <td class="py-3 text-zinc-400 text-[11px]">
                            <?= Crypto::formatMoney($tx['balance_before']) ?> &rarr; <strong class="text-white"><?= Crypto::formatMoney($tx['balance_after']) ?></strong>
                        </td>
                        <td class="py-3">
                            <span class="px-2 py-0.5 rounded text-[10px] uppercase font-bold
                                <?= match($tx['status']) {
                                    'approved' => 'bg-emerald-500/20 text-emerald-300',
                                    'pending'  => 'bg-amber-500/20 text-amber-300',
                                    default    => 'bg-rose-500/20 text-rose-300',
                                } ?>">
                                <?= $tx['status'] ?>
                            </span>
                        </td>
                        <td class="py-3 text-zinc-500"><?= date('M d, Y H:i', strtotime($tx['created_at'])) ?></td>
                    </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
$slot = ob_get_clean();
require __DIR__ . '/../layouts/admin.php';
?>
