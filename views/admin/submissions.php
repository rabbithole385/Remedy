<?php
$pageTitle = "Task Submissions Review";
$currentRoute = "admin_submissions";
ob_start();
?>

<!-- Header & Filter -->
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
    <div>
        <h1 class="text-2xl font-black text-white tracking-tight">Task Submissions Review</h1>
        <p class="text-sm text-zinc-400 mt-1">Verify submitted screenshots and URLs. Approvals will instantly credit user balances.</p>
    </div>
    <div class="flex items-center gap-2">
        <a href="admin/submissions" class="px-3 py-1.5 rounded-xl text-xs font-semibold <?= empty($statusFilter) ? 'bg-amber-500 text-black' : 'bg-white/5 text-zinc-400' ?>">All</a>
        <a href="admin/submissions?status=pending" class="px-3 py-1.5 rounded-xl text-xs font-semibold <?= $statusFilter === 'pending' ? 'bg-amber-500 text-black' : 'bg-white/5 text-zinc-400' ?>">Pending</a>
        <a href="admin/submissions?status=approved" class="px-3 py-1.5 rounded-xl text-xs font-semibold <?= $statusFilter === 'approved' ? 'bg-amber-500 text-black' : 'bg-white/5 text-zinc-400' ?>">Approved</a>
        <a href="admin/submissions?status=rejected" class="px-3 py-1.5 rounded-xl text-xs font-semibold <?= $statusFilter === 'rejected' ? 'bg-amber-500 text-black' : 'bg-white/5 text-zinc-400' ?>">Rejected</a>
    </div>
</div>

<div class="stat-card rounded-2xl p-6" x-data="{
    proofModal: false,
    proofSrc: '',
    rejectModal: false,
    rejectId: 0,
    openProof(src) {
        this.proofSrc = src;
        this.proofModal = true;
    },
    openReject(id) {
        this.rejectId = id;
        this.rejectModal = true;
    }
}">

    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs">
            <thead>
                <tr class="text-zinc-400 uppercase tracking-wider border-b border-white/5 pb-3">
                    <th class="pb-3 font-semibold">User</th>
                    <th class="pb-3 font-semibold">Task Title</th>
                    <th class="pb-3 font-semibold">Reward</th>
                    <th class="pb-3 font-semibold">Proof Type</th>
                    <th class="pb-3 font-semibold">Proof Content</th>
                    <th class="pb-3 font-semibold">Status</th>
                    <th class="pb-3 font-semibold">Submitted</th>
                    <th class="pb-3 font-semibold text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                <?php if (empty($submissions)): ?>
                    <tr><td colspan="8" class="py-8 text-center text-zinc-500">No submissions found.</td></tr>
                <?php else: foreach ($submissions as $sub): ?>
                    <tr class="hover:bg-white/[0.02] transition-colors">
                        <td class="py-3.5">
                            <span class="font-bold text-white block"><?= htmlspecialchars($sub['username']) ?></span>
                            <span class="text-[10px] text-zinc-500"><?= htmlspecialchars($sub['email']) ?></span>
                        </td>
                        <td class="py-3.5 font-medium text-white"><?= htmlspecialchars($sub['task_title']) ?></td>
                        <td class="py-3.5 font-bold text-emerald-400">+<?= Crypto::formatMoney($sub['task_reward']) ?></td>
                        <td class="py-3.5 capitalize text-zinc-400"><?= htmlspecialchars($sub['proof_type']) ?></td>
                        <td class="py-3.5">
                            <?php if ($sub['proof_type'] === 'screenshot'): ?>
                                <button type="button" @click="openProof('<?= htmlspecialchars($sub['proof_value']) ?>')"
                                    class="px-2.5 py-1 rounded-lg bg-sky-500/10 hover:bg-sky-500/20 text-sky-400 font-semibold inline-flex items-center gap-1 text-[11px]">
                                    <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                                    <span>View Image</span>
                                </button>
                            <?php else: ?>
                                <a href="<?= htmlspecialchars($sub['proof_value']) ?>" target="_blank" class="text-sky-400 underline font-mono text-[11px] truncate block max-w-xs">
                                    <?= htmlspecialchars($sub['proof_value']) ?>
                                </a>
                            <?php endif; ?>
                        </td>
                        <td class="py-3.5">
                            <span class="px-2 py-0.5 rounded text-[10px] uppercase font-bold
                                <?= match($sub['status']) {
                                    'approved' => 'bg-emerald-500/20 text-emerald-300',
                                    'pending'  => 'bg-amber-500/20 text-amber-300',
                                    default    => 'bg-rose-500/20 text-rose-300',
                                } ?>">
                                <?= $sub['status'] ?>
                            </span>
                        </td>
                        <td class="py-3.5 text-zinc-500"><?= date('M d, H:i', strtotime($sub['created_at'])) ?></td>
                        <td class="py-3.5 text-right space-x-2">
                            <?php if ($sub['status'] === 'pending'): ?>
                                <!-- Approve Form -->
                                <form action="admin/submissions/process" method="POST" class="inline" onsubmit="return confirm('Approve this submission and credit user balance?')">
                                    <?= Csrf::field() ?>
                                    <input type="hidden" name="submission_id" value="<?= $sub['id'] ?>">
                                    <input type="hidden" name="action" value="approve">
                                    <button type="submit" class="px-2.5 py-1 rounded-lg bg-emerald-500 hover:bg-emerald-400 text-black text-[11px] font-bold transition-colors">
                                        Approve
                                    </button>
                                </form>

                                <!-- Reject Button -->
                                <button type="button" @click="openReject(<?= $sub['id'] ?>)"
                                    class="px-2.5 py-1 rounded-lg bg-rose-500/20 hover:bg-rose-500/30 text-rose-300 text-[11px] font-semibold transition-colors">
                                    Reject
                                </button>
                            <?php else: ?>
                                <span class="text-[11px] text-zinc-500">Processed</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Proof Preview Modal -->
    <div x-show="proofModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm" x-cloak>
        <div @click.away="proofModal = false" class="bg-zinc-900 border border-white/10 rounded-2xl p-4 max-w-2xl w-full shadow-2xl">
            <div class="flex items-center justify-between pb-2 border-b border-white/5 mb-3">
                <span class="text-xs font-bold text-white uppercase tracking-wider">Proof Preview</span>
                <button type="button" @click="proofModal = false" class="text-zinc-400 hover:text-white">&times;</button>
            </div>
            <div class="max-h-[70vh] overflow-auto rounded-xl">
                <img :src="proofSrc" alt="Submitted Proof" class="w-full object-contain">
            </div>
        </div>
    </div>

    <!-- Reject Modal -->
    <div x-show="rejectModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm" x-cloak>
        <div @click.away="rejectModal = false" class="bg-zinc-900 border border-white/10 rounded-2xl p-6 max-w-md w-full shadow-2xl">
            <h3 class="text-base font-bold text-white mb-2">Reject Submission</h3>
            <p class="text-xs text-zinc-400 mb-4">Provide a reason so the user knows why this submission did not pass.</p>

            <form action="admin/submissions/process" method="POST" class="space-y-4">
                <?= Csrf::field() ?>
                <input type="hidden" name="submission_id" :value="rejectId">
                <input type="hidden" name="action" value="reject">

                <div>
                    <label class="block text-xs font-medium text-zinc-400 mb-1">Rejection Reason</label>
                    <textarea name="rejection_reason" rows="3" required
                        placeholder="e.g. Screenshot blurry, incomplete steps, invalid URL..."
                        class="w-full px-4 py-2 bg-zinc-800 border border-white/10 rounded-xl text-xs text-white focus:outline-none focus:border-rose-500"></textarea>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2">
                    <button type="button" @click="rejectModal = false" class="px-4 py-2 rounded-xl bg-zinc-800 text-xs font-semibold text-zinc-300">Cancel</button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-rose-500 hover:bg-rose-400 text-white text-xs font-bold transition-colors">Confirm Reject</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
$slot = ob_get_clean();
require __DIR__ . '/../layouts/admin.php';
?>
