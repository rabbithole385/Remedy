<?php
$pageTitle = "Referral Network";
$currentRoute = "referral";
ob_start();
?>

<!-- Header -->
<div class="mb-8">
    <h1 class="text-2xl font-black text-white tracking-tight">Affiliate & Referral Network</h1>
    <p class="text-sm text-zinc-400 mt-1">Invite friends or partners and earn a <?= $bonusPercent ?>% lifetime commission on their first deposit.</p>
</div>

<!-- Stats Row -->
<div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-8">
    <div class="stat-card rounded-2xl p-5">
        <span class="text-xs font-medium text-zinc-400 uppercase tracking-wider">Total Referred Members</span>
        <div class="text-2xl font-black text-white mt-1"><?= $totalCount ?></div>
        <p class="text-xs text-sky-400 mt-1">Direct downlines</p>
    </div>

    <div class="stat-card rounded-2xl p-5">
        <span class="text-xs font-medium text-zinc-400 uppercase tracking-wider">Total Commission Paid</span>
        <div class="text-2xl font-black text-emerald-400 mt-1"><?= Crypto::formatMoney($totalBonus) ?></div>
        <p class="text-xs text-zinc-400 mt-1">Credited to wallet</p>
    </div>

    <div class="stat-card rounded-2xl p-5">
        <span class="text-xs font-medium text-zinc-400 uppercase tracking-wider">Commission Rate</span>
        <div class="text-2xl font-black text-white mt-1"><?= $bonusPercent ?>%</div>
        <p class="text-xs text-purple-400 mt-1">Initial deposit reward</p>
    </div>
</div>

<!-- Referral Link Sharing Card -->
<div class="stat-card rounded-2xl p-6 sm:p-8 mb-8" x-data="{
    code: '<?= htmlspecialchars($referralCode) ?>',
    link: '<?= htmlspecialchars($referralLink) ?>',
    copiedCode: false,
    copiedLink: false
}">
    <h3 class="text-base font-bold text-white mb-2">Your Personal Invitation Links</h3>
    <p class="text-xs text-zinc-400 mb-6">Share your link across Telegram, WhatsApp, Twitter, or blogs to grow your passive rewards.</p>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Referral Link -->
        <div class="p-4 rounded-xl bg-zinc-900/80 border border-white/10">
            <span class="text-[11px] font-semibold text-zinc-400 uppercase tracking-wider block mb-2">Unique Referral Link</span>
            <div class="flex items-center gap-2">
                <input type="text" readonly :value="link"
                    class="flex-1 px-3 py-2 bg-black/40 border border-white/10 rounded-xl text-xs text-sky-300 select-all focus:outline-none">
                <button type="button" @click="navigator.clipboard.writeText(link); copiedLink = true; setTimeout(() => copiedLink = false, 2000)"
                    class="px-3.5 py-2 rounded-xl bg-sky-500 hover:bg-sky-400 text-white text-xs font-semibold transition-colors flex items-center gap-1 shadow-md">
                    <i data-lucide="copy" class="w-3.5 h-3.5"></i>
                    <span x-text="copiedLink ? 'Copied!' : 'Copy'"></span>
                </button>
            </div>
        </div>

        <!-- Referral Code -->
        <div class="p-4 rounded-xl bg-zinc-900/80 border border-white/10">
            <span class="text-[11px] font-semibold text-zinc-400 uppercase tracking-wider block mb-2">Referral Code</span>
            <div class="flex items-center gap-2">
                <input type="text" readonly :value="code"
                    class="flex-1 px-3 py-2 bg-black/40 border border-white/10 rounded-xl text-xs font-mono font-bold text-emerald-400 select-all focus:outline-none">
                <button type="button" @click="navigator.clipboard.writeText(code); copiedCode = true; setTimeout(() => copiedCode = false, 2000)"
                    class="px-3.5 py-2 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-white text-xs font-semibold transition-colors flex items-center gap-1 shadow-md">
                    <i data-lucide="copy" class="w-3.5 h-3.5"></i>
                    <span x-text="copiedCode ? 'Copied!' : 'Copy'"></span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Downline Members List -->
<div class="stat-card rounded-2xl p-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h3 class="text-base font-bold text-white">Your Referred Members</h3>
            <p class="text-xs text-zinc-400 mt-0.5">List of users who registered with your code</p>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs">
            <thead>
                <tr class="text-zinc-400 uppercase tracking-wider border-b border-white/5 pb-3">
                    <th class="pb-3 font-semibold">Username</th>
                    <th class="pb-3 font-semibold">Registered On</th>
                    <th class="pb-3 font-semibold">Bonus Status</th>
                    <th class="pb-3 font-semibold">Commission Earned</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                <?php if (empty($referrals)): ?>
                    <tr>
                        <td colspan="4" class="py-8 text-center text-zinc-500">
                            You have not invited any members yet. Share your link above to start earning!
                        </td>
                    </tr>
                <?php else: foreach ($referrals as $ref): ?>
                    <tr class="hover:bg-white/[0.02] transition-colors">
                        <td class="py-3.5 font-medium text-white"><?= htmlspecialchars($ref['username']) ?></td>
                        <td class="py-3.5 text-zinc-400"><?= date('M d, Y', strtotime($ref['joined_at'])) ?></td>
                        <td class="py-3.5">
                            <span class="px-2 py-0.5 rounded text-[10px] uppercase font-bold
                                <?= $ref['bonus_paid'] ? 'bg-emerald-500/20 text-emerald-300' : 'bg-amber-500/20 text-amber-300' ?>">
                                <?= $ref['bonus_paid'] ? 'Paid' : 'Pending First Deposit' ?>
                            </span>
                        </td>
                        <td class="py-3.5 font-bold text-emerald-400">
                            +<?= Crypto::formatMoney($ref['bonus_amount']) ?>
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
