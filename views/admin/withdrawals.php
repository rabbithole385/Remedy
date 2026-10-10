<?php
$pageTitle = "Withdrawal Requests";
$currentRoute = "admin_withdrawals";
ob_start();
?>

<!-- Header -->
<div class="mb-8">
    <h1 class="text-2xl font-black text-white tracking-tight">Withdrawal Requests & Payouts</h1>
    <p class="text-sm text-zinc-400 mt-1">Approve crypto payouts or reject them (rejecting automatically refunds funds back to user account balance).</p>
</div>

<div class="stat-card rounded-2xl p-6" x-data="{
    approveModal: false,
    selectedId: 0,
    txHash: '',
    openApprove(id) {
        this.selectedId = id;
        this.txHash = '';
        this.approveModal = true;
    }
}">

    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs">
            <thead>
                <tr class="text-zinc-400 uppercase tracking-wider border-b border-white/5 pb-3">
                    <th class="pb-3 font-semibold">User</th>
                    <th class="pb-3 font-semibold">Reference</th>
                    <th class="pb-3 font-semibold">Gross Amount</th>
                    <th class="pb-3 font-semibold">Fee</th>
                    <th class="pb-3 font-semibold">Net Payout</th>
                    <th class="pb-3 font-semibold">Recipient Address</th>
                    <th class="pb-3 font-semibold">Status</th>
                    <th class="pb-3 font-semibold">Requested At</th>
                    <th class="pb-3 font-semibold text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                <?php if (empty($withdrawals)): ?>
                    <tr><td colspan="9" class="py-8 text-center text-zinc-500">No withdrawal requests found.</td></tr>
                <?php else: foreach ($withdrawals as $w): ?>
                    <tr class="hover:bg-white/[0.02] transition-colors">
                        <td class="py-3.5">
                            <span class="font-bold text-white block"><?= htmlspecialchars($w['username']) ?></span>
                            <span class="text-[10px] text-zinc-500">Cur: <?= Crypto::formatMoney($w['current_balance']) ?></span>
                        </td>
                        <td class="py-3.5 font-mono text-zinc-300"><?= htmlspecialchars($w['reference']) ?></td>
                        <td class="py-3.5 text-zinc-300"><?= Crypto::formatMoney($w['amount']) ?></td>
                        <td class="py-3.5 text-rose-400">-<?= Crypto::formatMoney($w['fee']) ?></td>
                        <td class="py-3.5 font-black text-emerald-400 text-sm">
                            <?= Crypto::formatMoney($w['net_amount']) ?>
                        </td>
                        <td class="py-3.5 font-mono text-zinc-300">
                            <span class="px-1.5 py-0.5 rounded text-[10px] bg-zinc-800 text-sky-400 mr-1"><?= htmlspecialchars($w['crypto_type'] ?? 'USDT') ?></span>
                            <span title="<?= htmlspecialchars($w['crypto_address']) ?>"><?= Crypto::truncate($w['crypto_address']) ?></span>
                        </td>
                        <td class="py-3.5">
                            <span class="px-2 py-0.5 rounded text-[10px] uppercase font-bold
                                <?= match($w['status']) {
                                    'approved' => 'bg-emerald-500/20 text-emerald-300',
                                    'pending'  => 'bg-amber-500/20 text-amber-300',
                                    default    => 'bg-rose-500/20 text-rose-300',
                                } ?>">
                                <?= $w['status'] ?>
                            </span>
                        </td>
                        <td class="py-3.5 text-zinc-500"><?= date('M d, H:i', strtotime($w['created_at'])) ?></td>
                        <td class="py-3.5 text-right space-x-2">
                            <?php if ($w['status'] === 'pending'): ?>
                                <button type="button" @click="openApprove(<?= $w['id'] ?>)"
                                    class="px-2.5 py-1 rounded-lg bg-emerald-500 hover:bg-emerald-400 text-black text-[11px] font-bold transition-colors">
                                    Send / Approve
                                </button>

                                <form action="admin/withdrawals/process" method="POST" class="inline" onsubmit="return confirm('Reject and refund balance to user?')">
                                    <?= Csrf::field() ?>
                                    <input type="hidden" name="tx_id" value="<?= $w['id'] ?>">
                                    <input type="hidden" name="action" value="reject">
                                    <button type="submit" class="px-2.5 py-1 rounded-lg bg-rose-500/20 hover:bg-rose-500/30 text-rose-300 text-[11px] font-semibold transition-colors">
                                        Reject & Refund
                                    </button>
                                </form>
                            <?php else: ?>
                                <span class="text-[11px] text-zinc-500">Processed</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Approve Modal -->
    <div x-show="approveModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm" x-cloak>
        <div @click.away="approveModal = false" class="bg-zinc-900 border border-white/10 rounded-2xl p-6 max-w-md w-full shadow-2xl">
            <h3 class="text-base font-bold text-white mb-2">Confirm Withdrawal Execution</h3>
            <p class="text-xs text-zinc-400 mb-4">Paste the payout transaction hash below so the user can verify the transfer on the blockchain explorer.</p>

            <form action="admin/withdrawals/process" method="POST" class="space-y-4">
                <?= Csrf::field() ?>
                <input type="hidden" name="tx_id" :value="selectedId">
                <input type="hidden" name="action" value="approve">

                <div>
                    <label class="block text-xs font-medium text-zinc-400 mb-1">Outgoing Transaction Hash (TXID)</label>
                    <input type="text" name="tx_hash" x-model="txHash"
                        placeholder="e.g. 0x82f... or TRX hash"
                        class="w-full px-4 py-2 bg-zinc-800 border border-white/10 rounded-xl text-xs font-mono text-white focus:outline-none focus:border-emerald-500">
                </div>

                <div class="flex items-center justify-end gap-2 pt-2">
                    <button type="button" @click="approveModal = false" class="px-4 py-2 rounded-xl bg-zinc-800 text-xs font-semibold text-zinc-300">Cancel</button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-black text-xs font-bold transition-colors">Mark as Sent</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
$slot = ob_get_clean();
require __DIR__ . '/../layouts/admin.php';
?>
