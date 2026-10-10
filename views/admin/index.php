<?php
$pageTitle = "Admin Overview";
$currentRoute = "admin_index";
ob_start();
?>

<!-- Stat Cards Grid -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
    <!-- Total Users -->
    <div class="stat-card rounded-2xl p-5">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-medium text-zinc-400 uppercase tracking-wider">Registered Users</span>
            <div class="w-9 h-9 rounded-xl bg-sky-500/10 border border-sky-500/20 flex items-center justify-center text-sky-400">
                <i data-lucide="users" class="w-4 h-4"></i>
            </div>
        </div>
        <div class="text-2xl font-black text-white"><?= $totalUsers ?></div>
        <a href="admin/users" class="text-xs text-sky-400 hover:underline mt-2 inline-block">Manage users &rarr;</a>
    </div>

    <!-- Total Deposits -->
    <div class="stat-card rounded-2xl p-5">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-medium text-zinc-400 uppercase tracking-wider">Approved Deposits</span>
            <div class="w-9 h-9 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400">
                <i data-lucide="arrow-down-left" class="w-4 h-4"></i>
            </div>
        </div>
        <div class="text-2xl font-black text-emerald-400"><?= Crypto::formatMoney($totalDeposits) ?></div>
        <div class="flex items-center gap-1.5 mt-2 text-xs">
            <?php if ($pendingDepositsCount > 0): ?>
                <span class="px-2 py-0.5 rounded bg-amber-500/20 text-amber-300 font-bold"><?= $pendingDepositsCount ?> pending</span>
                <a href="admin/deposits" class="text-amber-400 hover:underline">Review &rarr;</a>
            <?php else: ?>
                <span class="text-zinc-500">All deposits reviewed</span>
            <?php endif; ?>
        </div>
    </div>

    <!-- Total Withdrawals -->
    <div class="stat-card rounded-2xl p-5">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-medium text-zinc-400 uppercase tracking-wider">Approved Payouts</span>
            <div class="w-9 h-9 rounded-xl bg-rose-500/10 border border-rose-500/20 flex items-center justify-center text-rose-400">
                <i data-lucide="arrow-up-right" class="w-4 h-4"></i>
            </div>
        </div>
        <div class="text-2xl font-black text-white"><?= Crypto::formatMoney($totalWithdrawals) ?></div>
        <div class="flex items-center gap-1.5 mt-2 text-xs">
            <?php if ($pendingWithdrawalsCount > 0): ?>
                <span class="px-2 py-0.5 rounded bg-rose-500/20 text-rose-300 font-bold"><?= $pendingWithdrawalsCount ?> pending</span>
                <a href="admin/withdrawals" class="text-rose-400 hover:underline">Process &rarr;</a>
            <?php else: ?>
                <span class="text-zinc-500">All payouts processed</span>
            <?php endif; ?>
        </div>
    </div>

    <!-- Pending Task Submissions -->
    <div class="stat-card rounded-2xl p-5">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-medium text-zinc-400 uppercase tracking-wider">Task Approvals</span>
            <div class="w-9 h-9 rounded-xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center text-amber-400">
                <i data-lucide="check-square" class="w-4 h-4"></i>
            </div>
        </div>
        <div class="text-2xl font-black text-white"><?= $pendingSubmissionsCount ?></div>
        <a href="admin/submissions" class="text-xs text-amber-400 hover:underline mt-2 inline-block">Review submissions &rarr;</a>
    </div>
</div>

<!-- Recent Users & Transactions Overview -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <!-- Recent Users -->
    <div class="stat-card rounded-2xl p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-base font-bold text-white">Recently Joined Users</h3>
            <a href="admin/users" class="text-xs text-amber-400 hover:underline font-semibold">View All</a>
        </div>
        <div class="space-y-3">
            <?php foreach ($recentUsers as $u): ?>
                <div class="flex items-center justify-between p-3 rounded-xl bg-white/5 border border-white/5">
                    <div>
                        <p class="text-xs font-bold text-white"><?= htmlspecialchars($u['username']) ?></p>
                        <p class="text-[11px] text-zinc-400"><?= htmlspecialchars($u['email']) ?></p>
                    </div>
                    <div class="text-right">
                        <span class="text-xs font-bold text-emerald-400"><?= Crypto::formatMoney($u['balance']) ?></span>
                        <span class="block text-[10px] text-zinc-500"><?= date('M d, Y', strtotime($u['created_at'])) ?></span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="stat-card rounded-2xl p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-base font-bold text-white">Latest System Transactions</h3>
            <a href="admin/transactions" class="text-xs text-amber-400 hover:underline font-semibold">View All</a>
        </div>
        <div class="space-y-3">
            <?php foreach ($recentTransactions as $tx): ?>
                <div class="flex items-center justify-between p-3 rounded-xl bg-white/5 border border-white/5">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-semibold text-white"><?= htmlspecialchars($tx['username']) ?></span>
                            <span class="px-1.5 py-0.5 rounded text-[9px] font-bold uppercase bg-zinc-800 text-zinc-400"><?= str_replace('_', ' ', $tx['type']) ?></span>
                        </div>
                        <span class="text-[10px] text-zinc-500 font-mono"><?= htmlspecialchars($tx['reference']) ?></span>
                    </div>
                    <div class="text-right">
                        <span class="text-xs font-bold text-white"><?= Crypto::formatMoney($tx['net_amount']) ?></span>
                        <span class="block text-[10px] font-bold uppercase <?= match($tx['status']) {
                            'approved' => 'text-emerald-400',
                            'pending'  => 'text-amber-400',
                            default    => 'text-rose-400',
                        } ?>"><?= $tx['status'] ?></span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php
$slot = ob_get_clean();
require __DIR__ . '/../layouts/admin.php';
?>
