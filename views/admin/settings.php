<?php
$pageTitle = "System & Wallet Settings";
$currentRoute = "admin_settings";
ob_start();
?>

<!-- Header -->
<div class="mb-8">
    <h1 class="text-2xl font-black text-white tracking-tight">Platform Configuration & Wallet Addresses</h1>
    <p class="text-sm text-zinc-400 mt-1">Configure company cryptocurrency receiving addresses, fees, and affiliate commissions.</p>
</div>

<div class="max-w-4xl mx-auto stat-card rounded-2xl p-6 sm:p-8">
    <form action="admin/settings/save" method="POST" class="space-y-8">
        <?= Csrf::field() ?>

        <!-- Official Crypto Wallet Addresses -->
        <div>
            <h3 class="text-base font-bold text-white mb-1 flex items-center gap-2">
                <i data-lucide="wallet" class="w-4 h-4 text-amber-400"></i>
                Official Deposit Wallets
            </h3>
            <p class="text-xs text-zinc-400 mb-4">These addresses are shown to users on the Deposit page for payments.</p>

            <div class="space-y-4">
                <div>
                    <label for="usdt_trc20_wallet" class="block text-xs font-semibold text-zinc-300 mb-1">
                        USDT (TRC-20) Receiving Wallet
                    </label>
                    <input type="text" id="usdt_trc20_wallet" name="usdt_trc20_wallet"
                        value="<?= htmlspecialchars($settings['usdt_trc20_wallet'] ?? '') ?>"
                        placeholder="e.g. TRemedyUSDTTRC20OfficialWallet2026"
                        class="w-full px-4 py-2.5 bg-zinc-900 border border-white/10 rounded-xl text-xs font-mono text-white focus:outline-none focus:border-amber-500">
                </div>

                <div>
                    <label for="usdt_erc20_wallet" class="block text-xs font-semibold text-zinc-300 mb-1">
                        USDT (ERC-20) Receiving Wallet
                    </label>
                    <input type="text" id="usdt_erc20_wallet" name="usdt_erc20_wallet"
                        value="<?= htmlspecialchars($settings['usdt_erc20_wallet'] ?? '') ?>"
                        placeholder="e.g. 0xRemedyUSDTERC20OfficialWallet2026"
                        class="w-full px-4 py-2.5 bg-zinc-900 border border-white/10 rounded-xl text-xs font-mono text-white focus:outline-none focus:border-amber-500">
                </div>

                <div>
                    <label for="btc_wallet" class="block text-xs font-semibold text-zinc-300 mb-1">
                        Bitcoin (BTC) Receiving Wallet
                    </label>
                    <input type="text" id="btc_wallet" name="btc_wallet"
                        value="<?= htmlspecialchars($settings['btc_wallet'] ?? '') ?>"
                        placeholder="e.g. 1RemedyBTCOfficialWalletAddress2026"
                        class="w-full px-4 py-2.5 bg-zinc-900 border border-white/10 rounded-xl text-xs font-mono text-white focus:outline-none focus:border-amber-500">
                </div>
            </div>
        </div>

        <div class="border-t border-white/5 pt-6"></div>

        <!-- Financial Limits & Commissions -->
        <div>
            <h3 class="text-base font-bold text-white mb-1 flex items-center gap-2">
                <i data-lucide="percent" class="w-4 h-4 text-emerald-400"></i>
                Financial Limits & Commissions
            </h3>
            <p class="text-xs text-zinc-400 mb-4">Control deposit/withdrawal thresholds and system rates.</p>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="min_deposit" class="block text-xs font-semibold text-zinc-300 mb-1">Minimum Deposit ($)</label>
                    <input type="number" step="0.01" id="min_deposit" name="min_deposit"
                        value="<?= htmlspecialchars($settings['min_deposit'] ?? '10.00') ?>"
                        class="w-full px-4 py-2 bg-zinc-900 border border-white/10 rounded-xl text-xs text-white focus:outline-none focus:border-amber-500">
                </div>

                <div>
                    <label for="min_withdrawal" class="block text-xs font-semibold text-zinc-300 mb-1">Minimum Withdrawal ($)</label>
                    <input type="number" step="0.01" id="min_withdrawal" name="min_withdrawal"
                        value="<?= htmlspecialchars($settings['min_withdrawal'] ?? '5.00') ?>"
                        class="w-full px-4 py-2 bg-zinc-900 border border-white/10 rounded-xl text-xs text-white focus:outline-none focus:border-amber-500">
                </div>

                <div>
                    <label for="withdrawal_fee_percent" class="block text-xs font-semibold text-zinc-300 mb-1">Withdrawal Fee (%)</label>
                    <input type="number" step="0.01" id="withdrawal_fee_percent" name="withdrawal_fee_percent"
                        value="<?= htmlspecialchars($settings['withdrawal_fee_percent'] ?? '2.00') ?>"
                        class="w-full px-4 py-2 bg-zinc-900 border border-white/10 rounded-xl text-xs text-white focus:outline-none focus:border-amber-500">
                </div>

                <div>
                    <label for="referral_bonus_percent" class="block text-xs font-semibold text-zinc-300 mb-1">Referral Commission (%)</label>
                    <input type="number" step="0.01" id="referral_bonus_percent" name="referral_bonus_percent"
                        value="<?= htmlspecialchars($settings['referral_bonus_percent'] ?? '5.00') ?>"
                        class="w-full px-4 py-2 bg-zinc-900 border border-white/10 rounded-xl text-xs text-white focus:outline-none focus:border-amber-500">
                </div>
            </div>
        </div>

        <div class="border-t border-white/5 pt-6"></div>

        <!-- General Info -->
        <div>
            <h3 class="text-base font-bold text-white mb-1 flex items-center gap-2">
                <i data-lucide="globe" class="w-4 h-4 text-sky-400"></i>
                General Platform Info
            </h3>
            <p class="text-xs text-zinc-400 mb-4">Site branding and public contact emails.</p>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="site_name" class="block text-xs font-semibold text-zinc-300 mb-1">Site Title</label>
                    <input type="text" id="site_name" name="site_name"
                        value="<?= htmlspecialchars($settings['site_name'] ?? 'Remedy Investment') ?>"
                        class="w-full px-4 py-2 bg-zinc-900 border border-white/10 rounded-xl text-xs text-white focus:outline-none focus:border-amber-500">
                </div>

                <div>
                    <label for="site_email" class="block text-xs font-semibold text-zinc-300 mb-1">Support Email</label>
                    <input type="email" id="site_email" name="site_email"
                        value="<?= htmlspecialchars($settings['site_email'] ?? 'support@remedyinvestment.com') ?>"
                        class="w-full px-4 py-2 bg-zinc-900 border border-white/10 rounded-xl text-xs text-white focus:outline-none focus:border-amber-500">
                </div>
            </div>
        </div>

        <div class="pt-4">
            <button type="submit" class="px-6 py-3 rounded-xl bg-amber-500 hover:bg-amber-400 text-black text-xs font-bold transition-colors shadow-lg flex items-center gap-2">
                <i data-lucide="check" class="w-4 h-4"></i>
                <span>Save All Settings</span>
            </button>
        </div>
    </form>
</div>

<?php
$slot = ob_get_clean();
require __DIR__ . '/../layouts/admin.php';
?>
