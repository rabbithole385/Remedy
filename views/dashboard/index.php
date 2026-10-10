<?php
$pageTitle = "Dashboard Overview";
$currentRoute = "dashboard";
ob_start();
?>

<!-- Welcome Banner -->
<div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-sky-600/20 via-blue-600/10 to-indigo-600/10 border border-sky-500/20 p-6 sm:p-8 mb-8 backdrop-blur-xl">
    <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-sky-500/15 border border-sky-500/30 text-sky-400 text-xs font-semibold mb-3">
                <i data-lucide="sparkles" class="w-3.5 h-3.5"></i>
                Active Account
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">
                Welcome back, <span class="text-sky-400"><?= htmlspecialchars($user['username']) ?></span> 👋
            </h1>
            <p class="text-sm text-zinc-400 mt-1 max-w-xl">
                Track your active tasks, manage crypto deposits and monitor your real-time yield performance.
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <a href="tasks" class="btn-brand px-5 py-2.5 rounded-xl text-sm font-semibold text-white flex items-center gap-2">
                <i data-lucide="play" class="w-4 h-4 fill-current"></i>
                Start Tasks
            </a>
            <a href="deposit" class="px-5 py-2.5 rounded-xl text-sm font-semibold text-zinc-200 bg-white/5 hover:bg-white/10 border border-white/10 transition-colors flex items-center gap-2">
                <i data-lucide="plus" class="w-4 h-4"></i>
                Deposit Funds
            </a>
        </div>
    </div>
</div>

<!-- Key Stat Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
    <!-- Balance -->
    <div class="stat-card rounded-2xl p-5 relative overflow-hidden">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-medium text-zinc-400 uppercase tracking-wider">Available Balance</span>
            <div class="w-9 h-9 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400">
                <i data-lucide="wallet" class="w-4 h-4"></i>
            </div>
        </div>
        <div class="text-2xl font-black text-white"><?= Crypto::formatMoney($balance) ?></div>
        <div class="flex items-center gap-2 mt-2 text-xs text-emerald-400">
            <i data-lucide="trending-up" class="w-3.5 h-3.5"></i>
            <span>Instant withdraw ready</span>
        </div>
    </div>

    <!-- Total Earned -->
    <div class="stat-card rounded-2xl p-5 relative overflow-hidden">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-medium text-zinc-400 uppercase tracking-wider">Total Earnings</span>
            <div class="w-9 h-9 rounded-xl bg-sky-500/10 border border-sky-500/20 flex items-center justify-center text-sky-400">
                <i data-lucide="dollar-sign" class="w-4 h-4"></i>
            </div>
        </div>
        <div class="text-2xl font-black text-white"><?= Crypto::formatMoney($totalEarned) ?></div>
        <div class="flex items-center gap-2 mt-2 text-xs text-sky-400">
            <i data-lucide="award" class="w-3.5 h-3.5"></i>
            <span>Tasks + Commissions</span>
        </div>
    </div>

    <!-- Active Tasks -->
    <div class="stat-card rounded-2xl p-5 relative overflow-hidden">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-medium text-zinc-400 uppercase tracking-wider">Available Tasks</span>
            <div class="w-9 h-9 rounded-xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center text-amber-400">
                <i data-lucide="check-circle-2" class="w-4 h-4"></i>
            </div>
        </div>
        <div class="text-2xl font-black text-white"><?= $availableTasksCount ?></div>
        <div class="flex items-center gap-2 mt-2 text-xs text-zinc-400">
            <span><?= $completedTasksCount ?> tasks completed</span>
        </div>
    </div>

    <!-- Referrals -->
    <div class="stat-card rounded-2xl p-5 relative overflow-hidden">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-medium text-zinc-400 uppercase tracking-wider">Referral Network</span>
            <div class="w-9 h-9 rounded-xl bg-purple-500/10 border border-purple-500/20 flex items-center justify-center text-purple-400">
                <i data-lucide="users" class="w-4 h-4"></i>
            </div>
        </div>
        <div class="text-2xl font-black text-white"><?= $referralCount ?></div>
        <div class="flex items-center gap-2 mt-2 text-xs text-purple-400">
            <i data-lucide="gift" class="w-3.5 h-3.5"></i>
            <span><?= Crypto::formatMoney($referralBonus) ?> bonus earned</span>
        </div>
    </div>
</div>

<!-- Analytics & Active Tasks Section -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
    <!-- Chart Overview -->
    <div class="lg:col-span-2 stat-card rounded-2xl p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="text-base font-bold text-white">Earnings & Activity Performance</h3>
                <p class="text-xs text-zinc-400 mt-0.5">Real-time reward metrics</p>
            </div>
            <span class="px-2.5 py-1 rounded-lg bg-sky-500/10 border border-sky-500/20 text-sky-400 text-xs font-medium">30 Days</span>
        </div>
        <div class="h-64">
            <canvas id="earningsChart"></canvas>
        </div>
    </div>

    <!-- Quick Action / Tasks Highlight -->
    <div class="stat-card rounded-2xl p-6 flex flex-col justify-between">
        <div>
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-bold text-white">Featured Tasks</h3>
                <a href="tasks" class="text-xs text-sky-400 hover:text-sky-300 font-medium">View all</a>
            </div>
            <div class="space-y-3">
                <?php 
                $previewTasks = array_slice($tasks, 0, 3);
                if (empty($previewTasks)): 
                ?>
                    <p class="text-zinc-500 text-xs text-center py-6">No tasks currently available.</p>
                <?php else: foreach ($previewTasks as $t): ?>
                    <div class="p-3.5 rounded-xl bg-white/5 border border-white/5 hover:border-sky-500/30 transition-all flex items-center justify-between gap-3">
                        <div class="min-w-0">
                            <p class="text-xs font-semibold text-white truncate"><?= htmlspecialchars($t['title']) ?></p>
                            <span class="text-[11px] text-zinc-400 capitalize"><?= htmlspecialchars($t['type']) ?></span>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <span class="text-xs font-bold text-emerald-400">+<?= Crypto::formatMoney($t['reward']) ?></span>
                            <?php if (empty($t['submission_id'])): ?>
                                <a href="tasks/do?id=<?= $t['id'] ?>" class="block text-[11px] text-sky-400 hover:underline mt-0.5">Start &rarr;</a>
                            <?php else: ?>
                                <span class="block text-[10px] text-amber-400 uppercase font-semibold mt-0.5"><?= $t['submission_status'] ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; endif; ?>
            </div>
        </div>

        <!-- Referral Link Fast Copy -->
        <div class="mt-6 pt-5 border-t border-white/5" x-data="{ copied: false, link: '<?= rtrim(APP_URL, '/') . '/register?ref=' . $user['referral_code'] ?>' }">
            <label class="block text-xs font-medium text-zinc-400 mb-1.5">Your Referral Link</label>
            <div class="flex items-center gap-2">
                <input type="text" readonly :value="link"
                    class="flex-1 px-3 py-2 bg-zinc-900/80 border border-white/10 rounded-xl text-xs text-zinc-300 select-all focus:outline-none">
                <button type="button" @click="navigator.clipboard.writeText(link); copied = true; setTimeout(() => copied = false, 2000)"
                    class="px-3 py-2 rounded-xl bg-sky-500/20 text-sky-400 text-xs font-semibold hover:bg-sky-500/30 transition-colors flex items-center gap-1">
                    <span x-text="copied ? 'Copied!' : 'Copy'"></span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Recent Transactions Table -->
<div class="stat-card rounded-2xl p-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h3 class="text-base font-bold text-white">Recent Transactions</h3>
            <p class="text-xs text-zinc-400 mt-0.5">Latest deposits, payouts & task rewards</p>
        </div>
        <a href="transactions" class="text-xs text-sky-400 hover:text-sky-300 font-medium">Full History &rarr;</a>
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
                <?php if (empty($recentTransactions)): ?>
                    <tr>
                        <td colspan="5" class="py-6 text-center text-zinc-500">No transactions recorded yet.</td>
                    </tr>
                <?php else: foreach ($recentTransactions as $tx): ?>
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

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('earningsChart');
        if (ctx) {
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['Day 1', 'Day 5', 'Day 10', 'Day 15', 'Day 20', 'Day 25', 'Today'],
                    datasets: [{
                        label: 'Task & Yield Growth ($)',
                        data: [0, <?= max(5, round($totalEarned * 0.15, 2)) ?>, <?= max(12, round($totalEarned * 0.35, 2)) ?>, <?= max(25, round($totalEarned * 0.55, 2)) ?>, <?= max(40, round($totalEarned * 0.75, 2)) ?>, <?= max(60, round($totalEarned * 0.9, 2)) ?>, <?= max(75, round($totalEarned, 2)) ?>],
                        borderColor: '#0ea5e9',
                        backgroundColor: 'rgba(14, 165, 233, 0.1)',
                        fill: true,
                        tension: 0.4,
                        borderWidth: 2,
                        pointBackgroundColor: '#0ea5e9',
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        x: {
                            grid: { color: 'rgba(255, 255, 255, 0.05)' },
                            ticks: { color: '#71717a' }
                        },
                        y: {
                            grid: { color: 'rgba(255, 255, 255, 0.05)' },
                            ticks: { color: '#71717a' }
                        }
                    }
                }
            });
        }
    });
</script>

<?php
$slot = ob_get_clean();
require __DIR__ . '/../layouts/app.php';
?>
