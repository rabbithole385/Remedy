<?php
$pageTitle = "Task Management";
$currentRoute = "admin_tasks";
ob_start();
?>

<!-- Header -->
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8" x-data="{
    modalOpen: false,
    editTask: { id: 0, title: '', description: '', instructions: '', type: 'other', reward: '', target_url: '', max_submissions: '', status: 'active' },
    openNewModal() {
        this.editTask = { id: 0, title: '', description: '', instructions: '', type: 'other', reward: '', target_url: '', max_submissions: '', status: 'active' };
        this.modalOpen = true;
    },
    openEditModal(task) {
        this.editTask = Object.assign({}, task);
        this.modalOpen = true;
    }
}">
    <div>
        <h1 class="text-2xl font-black text-white tracking-tight">Task & Mission Management</h1>
        <p class="text-sm text-zinc-400 mt-1">Create, update and configure earning tasks available to platform users.</p>
    </div>
    <button type="button" @click="openNewModal()"
        class="px-4 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-black text-xs font-bold transition-colors flex items-center gap-2 shadow-lg">
        <i data-lucide="plus" class="w-4 h-4"></i>
        <span>Create New Task</span>
    </button>

    <!-- Task CRUD Modal -->
    <div x-show="modalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/75 backdrop-blur-sm" x-cloak>
        <div @click.away="modalOpen = false" class="bg-zinc-900 border border-white/10 rounded-2xl p-6 sm:p-8 max-w-lg w-full max-h-[90vh] overflow-y-auto shadow-2xl">
            <div class="flex items-center justify-between pb-4 border-b border-white/5 mb-6">
                <h3 class="text-base font-bold text-white" x-text="editTask.id > 0 ? 'Edit Task #' + editTask.id : 'Create New Task'"></h3>
                <button type="button" @click="modalOpen = false" class="text-zinc-400 hover:text-white">&times;</button>
            </div>

            <form action="admin/tasks/save" method="POST" enctype="multipart/form-data" class="space-y-4">
                <?= Csrf::field() ?>
                <input type="hidden" name="id" :value="editTask.id">

                <div>
                    <label class="block text-xs font-medium text-zinc-400 mb-1">Task Title</label>
                    <input type="text" name="title" x-model="editTask.title" required
                        placeholder="e.g. Rate Mobile Application"
                        class="w-full px-4 py-2 bg-zinc-800 border border-white/10 rounded-xl text-xs text-white focus:outline-none focus:border-amber-500">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-zinc-400 mb-1">Category Type</label>
                        <select name="type" x-model="editTask.type"
                            class="w-full px-3 py-2 bg-zinc-800 border border-white/10 rounded-xl text-xs text-white focus:outline-none focus:border-amber-500">
                            <option value="rate">Rate & Review</option>
                            <option value="watch">Watch Media</option>
                            <option value="order">Order Completion</option>
                            <option value="survey">Survey</option>
                            <option value="social">Social Media</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-zinc-400 mb-1">Reward ($)</label>
                        <input type="number" name="reward" x-model="editTask.reward" step="0.01" min="0.01" required
                            placeholder="10.00"
                            class="w-full px-3 py-2 bg-zinc-800 border border-white/10 rounded-xl text-xs text-white focus:outline-none focus:border-amber-500">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-medium text-zinc-400 mb-1">Brief Description</label>
                    <textarea name="description" x-model="editTask.description" rows="2" required
                        placeholder="Short overview of what the user needs to accomplish..."
                        class="w-full px-4 py-2 bg-zinc-800 border border-white/10 rounded-xl text-xs text-white focus:outline-none focus:border-amber-500"></textarea>
                </div>

                <div>
                    <label class="block text-xs font-medium text-zinc-400 mb-1">Detailed Instructions</label>
                    <textarea name="instructions" x-model="editTask.instructions" rows="3"
                        placeholder="Step 1: Go to url... Step 2: Take screenshot..."
                        class="w-full px-4 py-2 bg-zinc-800 border border-white/10 rounded-xl text-xs text-white focus:outline-none focus:border-amber-500"></textarea>
                </div>

                <div>
                    <label class="block text-xs font-medium text-zinc-400 mb-1">Target URL (Optional)</label>
                    <input type="url" name="target_url" x-model="editTask.target_url"
                        placeholder="https://example.com/target"
                        class="w-full px-4 py-2 bg-zinc-800 border border-white/10 rounded-xl text-xs text-white focus:outline-none focus:border-amber-500">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-zinc-400 mb-1">Max Submissions (Blank = Unlimited)</label>
                        <input type="number" name="max_submissions" x-model="editTask.max_submissions" min="1"
                            class="w-full px-3 py-2 bg-zinc-800 border border-white/10 rounded-xl text-xs text-white focus:outline-none focus:border-amber-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-zinc-400 mb-1">Status</label>
                        <select name="status" x-model="editTask.status"
                            class="w-full px-3 py-2 bg-zinc-800 border border-white/10 rounded-xl text-xs text-white focus:outline-none focus:border-amber-500">
                            <option value="active">Active</option>
                            <option value="paused">Paused</option>
                            <option value="completed">Completed</option>
                        </select>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 pt-4">
                    <button type="button" @click="modalOpen = false" class="px-4 py-2 rounded-xl bg-zinc-800 text-xs font-semibold text-zinc-300">Cancel</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-amber-500 hover:bg-amber-400 text-black text-xs font-bold transition-colors">Save Task</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Table of Tasks -->
    <div class="stat-card rounded-2xl p-6 col-span-full w-full">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="text-zinc-400 uppercase tracking-wider border-b border-white/5 pb-3">
                        <th class="pb-3 font-semibold">Title</th>
                        <th class="pb-3 font-semibold">Type</th>
                        <th class="pb-3 font-semibold">Reward</th>
                        <th class="pb-3 font-semibold">Submissions</th>
                        <th class="pb-3 font-semibold">Status</th>
                        <th class="pb-3 font-semibold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    <?php if (empty($tasks)): ?>
                        <tr><td colspan="6" class="py-8 text-center text-zinc-500">No tasks created yet. Click 'Create New Task' to add one.</td></tr>
                    <?php else: foreach ($tasks as $t): ?>
                        <tr class="hover:bg-white/[0.02] transition-colors">
                            <td class="py-3.5 font-bold text-white"><?= htmlspecialchars($t['title']) ?></td>
                            <td class="py-3.5 capitalize text-zinc-300"><?= htmlspecialchars($t['type']) ?></td>
                            <td class="py-3.5 font-bold text-emerald-400">+<?= Crypto::formatMoney($t['reward']) ?></td>
                            <td class="py-3.5 text-zinc-400"><?= $t['total_submissions'] ?><?= !empty($t['max_submissions']) ? ' / ' . $t['max_submissions'] : '' ?></td>
                            <td class="py-3.5">
                                <span class="px-2 py-0.5 rounded text-[10px] uppercase font-bold
                                    <?= match($t['status']) {
                                        'active' => 'bg-emerald-500/20 text-emerald-300',
                                        'paused' => 'bg-amber-500/20 text-amber-300',
                                        default  => 'bg-zinc-800 text-zinc-400',
                                    } ?>">
                                    <?= $t['status'] ?>
                                </span>
                            </td>
                            <td class="py-3.5 text-right space-x-2">
                                <button type="button" @click="openEditModal(<?= htmlspecialchars(json_encode($t)) ?>)"
                                    class="px-2.5 py-1 rounded-lg bg-zinc-800 hover:bg-zinc-700 text-zinc-200 text-[11px] font-semibold">
                                    Edit
                                </button>
                                <form action="admin/tasks/delete" method="POST" class="inline" onsubmit="return confirm('Delete this task?')">
                                    <?= Csrf::field() ?>
                                    <input type="hidden" name="id" value="<?= $t['id'] ?>">
                                    <button type="submit" class="px-2.5 py-1 rounded-lg bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 text-[11px] font-semibold">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
$slot = ob_get_clean();
require __DIR__ . '/../layouts/admin.php';
?>
