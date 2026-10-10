<?php
$pageTitle = "User Account Management";
$currentRoute = "admin_users";
ob_start();
?>

<!-- Header & Search -->
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
    <div>
        <h1 class="text-2xl font-black text-white tracking-tight">User Account Management</h1>
        <p class="text-sm text-zinc-400 mt-1">View, search, ban/unban users and manually adjust user balances.</p>
    </div>
    <form action="admin/users" method="GET" class="flex items-center gap-2">
        <input type="text" name="q" value="<?= htmlspecialchars($_GET['q'] ?? '') ?>" placeholder="Search username / email..."
            class="px-4 py-2 bg-zinc-900 border border-white/10 rounded-xl text-xs text-white placeholder-zinc-500 focus:outline-none focus:border-amber-500">
        <button type="submit" class="px-4 py-2 bg-amber-500 hover:bg-amber-400 text-black text-xs font-bold rounded-xl transition-colors">
            Search
        </button>
    </form>
</div>

<!-- Users Table Card with Modal state -->
<div class="stat-card rounded-2xl p-6" x-data="{
    modalOpen: false,
    selectedUser: null,
    actionType: 'credit',
    amount: '',
    note: '',
    openBalanceModal(user) {
        this.selectedUser = user;
        this.modalOpen = true;
        this.amount = '';
        this.note = '';
    }
}">

    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs">
            <thead>
                <tr class="text-zinc-400 uppercase tracking-wider border-b border-white/5 pb-3">
                    <th class="pb-3 font-semibold">User</th>
                    <th class="pb-3 font-semibold">Contact</th>
                    <th class="pb-3 font-semibold">Role</th>
                    <th class="pb-3 font-semibold">Status</th>
                    <th class="pb-3 font-semibold">Balance</th>
                    <th class="pb-3 font-semibold">Referral Code</th>
                    <th class="pb-3 font-semibold">Joined</th>
                    <th class="pb-3 font-semibold text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                <?php if (empty($users)): ?>
                    <tr>
                        <td colspan="8" class="py-8 text-center text-zinc-500">No users match your search criteria.</td>
                    </tr>
                <?php else: foreach ($users as $u): ?>
                    <tr class="hover:bg-white/[0.02] transition-colors">
                        <td class="py-3.5">
                            <span class="font-bold text-white block"><?= htmlspecialchars($u['username']) ?></span>
                            <span class="text-[10px] text-zinc-500"><?= htmlspecialchars(trim(($u['first_name'] ?? '') . ' ' . ($u['last_name'] ?? '')) ?: 'No Name') ?></span>
                        </td>
                        <td class="py-3.5">
                            <span class="text-zinc-300 block"><?= htmlspecialchars($u['email']) ?></span>
                            <span class="text-[10px] text-zinc-500"><?= htmlspecialchars($u['phone'] ?? '—') ?></span>
                        </td>
                        <td class="py-3.5">
                            <span class="px-2 py-0.5 rounded text-[10px] uppercase font-bold <?= $u['role'] === 'admin' ? 'bg-amber-500/20 text-amber-300' : 'bg-zinc-800 text-zinc-400' ?>">
                                <?= $u['role'] ?>
                            </span>
                        </td>
                        <td class="py-3.5">
                            <span class="px-2 py-0.5 rounded text-[10px] uppercase font-bold <?= $u['status'] === 'active' ? 'bg-emerald-500/20 text-emerald-300' : 'bg-rose-500/20 text-rose-300' ?>">
                                <?= $u['status'] ?>
                            </span>
                        </td>
                        <td class="py-3.5 font-bold text-emerald-400">
                            <?= Crypto::formatMoney($u['balance']) ?>
                        </td>
                        <td class="py-3.5 font-mono text-zinc-400"><?= htmlspecialchars($u['referral_code']) ?></td>
                        <td class="py-3.5 text-zinc-500"><?= date('M d, Y', strtotime($u['created_at'])) ?></td>
                        <td class="py-3.5 text-right space-x-2">
                            <!-- Adjust Balance -->
                            <button type="button" @click="openBalanceModal(<?= htmlspecialchars(json_encode($u)) ?>)"
                                class="px-2.5 py-1 rounded-lg bg-sky-500/10 hover:bg-sky-500/20 text-sky-400 text-[11px] font-semibold transition-colors">
                                Adjust $
                            </button>

                            <!-- Ban / Unban -->
                            <?php if ($u['role'] !== 'admin'): ?>
                                <form action="admin/users/toggle-status" method="POST" class="inline">
                                    <?= Csrf::field() ?>
                                    <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                    <button type="submit" onclick="return confirm('Change status for this user?')"
                                        class="px-2.5 py-1 rounded-lg <?= $u['status'] === 'active' ? 'bg-rose-500/10 text-rose-400 hover:bg-rose-500/20' : 'bg-emerald-500/10 text-emerald-400 hover:bg-emerald-500/20' ?> text-[11px] font-semibold transition-colors">
                                        <?= $u['status'] === 'active' ? 'Suspend' : 'Activate' ?>
                                    </button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Balance Adjustment Modal -->
    <div x-show="modalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm" x-cloak>
        <div @click.away="modalOpen = false" class="bg-zinc-900 border border-white/10 rounded-2xl p-6 max-w-md w-full shadow-2xl">
            <div class="flex items-center justify-between pb-3 border-b border-white/5 mb-4">
                <h3 class="text-base font-bold text-white">Adjust User Balance</h3>
                <button type="button" @click="modalOpen = false" class="text-zinc-400 hover:text-white">&times;</button>
            </div>

            <form action="admin/users/balance" method="POST" class="space-y-4">
                <?= Csrf::field() ?>
                <input type="hidden" name="user_id" :value="selectedUser ? selectedUser.id : 0">

                <div class="p-3 rounded-xl bg-white/5 border border-white/5 text-xs">
                    <span class="text-zinc-400">Target User:</span>
                    <span class="font-bold text-white ml-1" x-text="selectedUser ? selectedUser.username : ''"></span>
                    <span class="text-zinc-500 block mt-0.5">Current Balance: <strong class="text-emerald-400" x-text="'$' + (selectedUser ? selectedUser.balance : 0)"></strong></span>
                </div>

                <div>
                    <label class="block text-xs font-medium text-zinc-400 mb-1">Action Type</label>
                    <div class="grid grid-cols-2 gap-2">
                        <button type="button" @click="actionType = 'credit'" :class="actionType === 'credit' ? 'bg-emerald-500 text-black font-bold' : 'bg-zinc-800 text-zinc-400'"
                            class="py-2 rounded-xl text-xs transition-colors">Credit (Add)</button>
                        <button type="button" @click="actionType = 'debit'" :class="actionType === 'debit' ? 'bg-rose-500 text-white font-bold' : 'bg-zinc-800 text-zinc-400'"
                            class="py-2 rounded-xl text-xs transition-colors">Debit (Deduct)</button>
                    </div>
                    <input type="hidden" name="action_type" :value="actionType">
                </div>

                <div>
                    <label class="block text-xs font-medium text-zinc-400 mb-1">Amount ($)</label>
                    <input type="number" name="amount" x-model="amount" step="0.01" min="0.01" required
                        placeholder="0.00"
                        class="w-full px-4 py-2.5 bg-zinc-800 border border-white/10 rounded-xl text-sm text-white focus:outline-none focus:border-amber-500">
                </div>

                <div>
                    <label class="block text-xs font-medium text-zinc-400 mb-1">Reason / Note</label>
                    <input type="text" name="note" x-model="note" required
                        placeholder="e.g. Deposit correction, promo bonus"
                        class="w-full px-4 py-2.5 bg-zinc-800 border border-white/10 rounded-xl text-sm text-white focus:outline-none focus:border-amber-500">
                </div>

                <div class="flex items-center justify-end gap-2 pt-2">
                    <button type="button" @click="modalOpen = false" class="px-4 py-2 rounded-xl bg-zinc-800 text-xs font-semibold text-zinc-300">Cancel</button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-400 text-black text-xs font-bold transition-colors">Confirm Adjustment</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
$slot = ob_get_clean();
require __DIR__ . '/../layouts/admin.php';
?>
