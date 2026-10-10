<?php
$pageTitle = "Crypto Deposits Review";
$currentRoute = "admin_deposits";
ob_start();
?>

<!-- Header -->
<div class="mb-8">
    <h1 class="text-2xl font-black text-white tracking-tight">Crypto Deposits Management</h1>
    <p class="text-sm text-zinc-400 mt-1">Verify blockchain transaction hashes and receipts. Approving will automatically fund the user account and credit referral commissions.</p>
</div>

<div class="stat-card rounded-2xl p-6" x-data="{
    proofModal: false,
    proofSrc: '',
    openProof(src) {
        this.proofSrc = src;
        this.proofModal = true;
    }
}">

    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs">
            <thead>
                <tr class="text-zinc-400 uppercase tracking-wider border-b border-white/5 pb-3">
                    <th class="pb-3 font-semibold">User</th>
                    <th class="pb-3 font-semibold">Reference</th>
                    <th class="pb-3 font-semibold">Asset / Network</th>
                    <th class="pb-3 font-semibold">Amount ($)</th>
                    <th class="pb-3 font-semibold">Tx Hash (TXID)</th>
                    <th class="pb-3 font-semibold">Receipt</th>
                    <th class="pb-3 font-semibold">Status</th>
                    <th class="pb-3 font-semibold">Date</th>
                    <th class="pb-3 font-semibold text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                <?php if (empty($deposits)): ?>
                    <tr><td colspan="9" class="py-8 text-center text-zinc-500">No deposits found.</td></tr>
                <?php else: foreach ($deposits as $tx): ?>
                    <tr class="hover:bg-white/[0.02] transition-colors">
                        <td class="py-3.5">
                            <span class="font-bold text-white block"><?= htmlspecialchars($tx['username']) ?></span>
                            <span class="text-[10px] text-zinc-500"><?= htmlspecialchars($tx['email']) ?></span>
                        </td>
                        <td class="py-3.5 font-mono text-zinc-300"><?= htmlspecialchars($tx['reference']) ?></td>
                        <td class="py-3.5">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-sky-500/20 text-sky-300">
                                <?= htmlspecialchars($tx['crypto_type'] ?? 'USDT') ?>
                            </span>
                        </td>
                        <td class="py-3.5 font-black text-emerald-400 text-sm">
                            <?= Crypto::formatMoney($tx['amount']) ?>
                        </td>
                        <td class="py-3.5 font-mono text-zinc-400">
                            <?php if (!empty($tx['tx_hash'])): ?>
                                <span title="<?= htmlspecialchars($tx['tx_hash']) ?>" class="cursor-pointer hover:text-white">
                                    <?= Crypto::truncate($tx['tx_hash'], 8, 6) ?>
                                </span>
                            <?php else: ?>
                                —
                            <?php endif; ?>
                        </td>
                        <td class="py-3.5">
                            <?php if (!empty($tx['proof_file'])): ?>
                                <button type="button" @click="openProof('<?= htmlspecialchars($tx['proof_file']) ?>')"
                                    class="px-2 py-1 rounded bg-zinc-800 hover:bg-zinc-700 text-zinc-200 text-[10px] font-semibold inline-flex items-center gap-1">
                                    <i data-lucide="image" class="w-3 h-3"></i>
                                    <span>Proof</span>
                                </button>
                            <?php else: ?>
                                <span class="text-zinc-600">None</span>
                            <?php endif; ?>
                        </td>
                        <td class="py-3.5">
                            <span class="px-2 py-0.5 rounded text-[10px] uppercase font-bold
                                <?= match($tx['status']) {
                                    'approved' => 'bg-emerald-500/20 text-emerald-300',
                                    'pending'  => 'bg-amber-500/20 text-amber-300',
                                    default    => 'bg-rose-500/20 text-rose-300',
                                } ?>">
                                <?= $tx['status'] ?>
                            </span>
                        </td>
                        <td class="py-3.5 text-zinc-500"><?= date('M d, H:i', strtotime($tx['created_at'])) ?></td>
                        <td class="py-3.5 text-right space-x-2">
                            <?php if ($tx['status'] === 'pending'): ?>
                                <form action="admin/deposits/process" method="POST" class="inline" onsubmit="return confirm('Approve deposit and credit user balance?')">
                                    <?= Csrf::field() ?>
                                    <input type="hidden" name="tx_id" value="<?= $tx['id'] ?>">
                                    <input type="hidden" name="action" value="approve">
                                    <button type="submit" class="px-2.5 py-1 rounded-lg bg-emerald-500 hover:bg-emerald-400 text-black text-[11px] font-bold transition-colors">
                                        Approve
                                    </button>
                                </form>

                                <form action="admin/deposits/process" method="POST" class="inline" onsubmit="return confirm('Reject this deposit?')">
                                    <?= Csrf::field() ?>
                                    <input type="hidden" name="tx_id" value="<?= $tx['id'] ?>">
                                    <input type="hidden" name="action" value="reject">
                                    <button type="submit" class="px-2.5 py-1 rounded-lg bg-rose-500/20 hover:bg-rose-500/30 text-rose-300 text-[11px] font-semibold transition-colors">
                                        Reject
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

    <!-- Receipt Modal -->
    <div x-show="proofModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm" x-cloak>
        <div @click.away="proofModal = false" class="bg-zinc-900 border border-white/10 rounded-2xl p-4 max-w-2xl w-full shadow-2xl">
            <div class="flex items-center justify-between pb-2 border-b border-white/5 mb-3">
                <span class="text-xs font-bold text-white uppercase tracking-wider">Deposit Screenshot Preview</span>
                <button type="button" @click="proofModal = false" class="text-zinc-400 hover:text-white">&times;</button>
            </div>
            <div class="max-h-[70vh] overflow-auto rounded-xl">
                <img :src="proofSrc" alt="Deposit Screenshot" class="w-full object-contain">
            </div>
        </div>
    </div>
</div>

<?php
$slot = ob_get_clean();
require __DIR__ . '/../layouts/admin.php';
?>
