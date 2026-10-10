<?php
$pageTitle = "Available Tasks";
$currentRoute = "tasks";
ob_start();
?>

<!-- Header -->
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
    <div>
        <h1 class="text-2xl font-black text-white tracking-tight">Available Tasks & Missions</h1>
        <p class="text-sm text-zinc-400 mt-1">Complete daily activities and get rewarded directly to your balance.</p>
    </div>
    <div class="flex items-center gap-2">
        <span class="px-3 py-1.5 rounded-xl bg-sky-500/10 border border-sky-500/20 text-xs font-semibold text-sky-400">
            <?= count($tasks) ?> Total Tasks
        </span>
    </div>
</div>

<!-- Task Cards Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
    <?php if (empty($tasks)): ?>
        <div class="col-span-full stat-card rounded-2xl p-12 text-center">
            <i data-lucide="inbox" class="w-12 h-12 text-zinc-600 mx-auto mb-3"></i>
            <h3 class="text-base font-bold text-white">No active tasks available right now</h3>
            <p class="text-xs text-zinc-400 mt-1">New tasks are refreshed daily. Please check back later.</p>
        </div>
    <?php else: foreach ($tasks as $task): ?>
        <div class="stat-card rounded-2xl p-6 flex flex-col justify-between border border-white/5 hover:border-sky-500/30 transition-all">
            <div>
                <!-- Top Type & Reward Badge -->
                <div class="flex items-center justify-between mb-4">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold uppercase tracking-wider
                        <?= match($task['type']) {
                            'rate'   => 'bg-amber-500/10 text-amber-400 border border-amber-500/20',
                            'watch'  => 'bg-red-500/10 text-red-400 border border-red-500/20',
                            'order'  => 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20',
                            'survey' => 'bg-indigo-500/10 text-indigo-400 border border-indigo-500/20',
                            'social' => 'bg-sky-500/10 text-sky-400 border border-sky-500/20',
                            default  => 'bg-zinc-500/10 text-zinc-400 border border-zinc-500/20',
                        } ?>">
                        <?= htmlspecialchars($task['type']) ?>
                    </span>
                    <span class="text-base font-extrabold text-emerald-400">
                        +<?= Crypto::formatMoney($task['reward']) ?>
                    </span>
                </div>

                <!-- Title & Description -->
                <h3 class="text-base font-bold text-white mb-2 leading-snug"><?= htmlspecialchars($task['title']) ?></h3>
                <p class="text-xs text-zinc-400 line-clamp-3 mb-4 leading-relaxed">
                    <?= htmlspecialchars($task['description']) ?>
                </p>
            </div>

            <!-- Footer Action -->
            <div class="pt-4 border-t border-white/5 flex items-center justify-between">
                <?php if (empty($task['submission_id'])): ?>
                    <span class="text-[11px] text-zinc-500">Not submitted</span>
                    <a href="tasks/do?id=<?= $task['id'] ?>" class="btn-brand px-4 py-2 rounded-xl text-xs font-semibold text-white flex items-center gap-1.5 shadow-md">
                        <span>Do Task</span>
                        <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                    </a>
                <?php else: ?>
                    <span class="text-[11px] text-zinc-400">Submission status:</span>
                    <span class="px-2.5 py-1 rounded-lg text-xs font-bold uppercase
                        <?= match($task['submission_status']) {
                            'approved' => 'bg-emerald-500/20 text-emerald-300',
                            'rejected' => 'bg-rose-500/20 text-rose-300',
                            default    => 'bg-amber-500/20 text-amber-300',
                        } ?>">
                        <?= $task['submission_status'] ?>
                    </span>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; endif; ?>
</div>

<!-- Task History Table -->
<div class="stat-card rounded-2xl p-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h3 class="text-base font-bold text-white">Your Submission History</h3>
            <p class="text-xs text-zinc-400 mt-0.5">Verification status of all tasks submitted by you</p>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs">
            <thead>
                <tr class="text-zinc-400 uppercase tracking-wider border-b border-white/5 pb-3">
                    <th class="pb-3 font-semibold">Task Name</th>
                    <th class="pb-3 font-semibold">Reward</th>
                    <th class="pb-3 font-semibold">Proof Type</th>
                    <th class="pb-3 font-semibold">Status</th>
                    <th class="pb-3 font-semibold">Submitted On</th>
                    <th class="pb-3 font-semibold">Notes / Reason</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                <?php if (empty($submissions)): ?>
                    <tr>
                        <td colspan="6" class="py-6 text-center text-zinc-500">You haven't submitted any tasks yet.</td>
                    </tr>
                <?php else: foreach ($submissions as $sub): ?>
                    <tr class="hover:bg-white/[0.02] transition-colors">
                        <td class="py-3 font-medium text-white"><?= htmlspecialchars($sub['task_title']) ?></td>
                        <td class="py-3 font-bold text-emerald-400">+<?= Crypto::formatMoney($sub['task_reward']) ?></td>
                        <td class="py-3 capitalize text-zinc-300"><?= htmlspecialchars($sub['proof_type']) ?></td>
                        <td class="py-3">
                            <span class="px-2 py-0.5 rounded text-[10px] uppercase font-bold
                                <?= match($sub['status']) {
                                    'approved' => 'bg-emerald-500/20 text-emerald-300',
                                    'pending'  => 'bg-amber-500/20 text-amber-300',
                                    'rejected' => 'bg-rose-500/20 text-rose-300',
                                    default    => 'bg-zinc-500/20 text-zinc-300',
                                } ?>">
                                <?= $sub['status'] ?>
                            </span>
                        </td>
                        <td class="py-3 text-zinc-400"><?= date('M d, Y H:i', strtotime($sub['created_at'])) ?></td>
                        <td class="py-3 text-zinc-400">
                            <?= !empty($sub['rejection_reason']) ? htmlspecialchars($sub['rejection_reason']) : '—' ?>
                        </td>
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
