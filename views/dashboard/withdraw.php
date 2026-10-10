<?php
$pageTitle = "Withdraw Funds";
$currentRoute = "withdraw";
ob_start();
?>

<div class="max-w-2xl mx-auto" x-data="{
    amount: '',
    feePercent: <?= $feePercent ?>,
    minWithdrawal: <?= $minWithdrawal ?>,
    balance: <?= $balance ?>,
    get fee() {
        let val = parseFloat(this.amount) || 0;
        return (val * (this.feePercent / 100)).toFixed(2);
    },
    get net() {
        let val = parseFloat(this.amount) || 0;
        let fee = parseFloat(this.fee);
        return Math.max(0, val - fee).toFixed(2);
    },
    loading: false
}">

    <!-- Header -->
    <div class="mb-8 text-center sm:text-left">
        <h1 class="text-2xl font-black text-white tracking-tight">Withdraw Funds</h1>
        <p class="text-sm text-zinc-400 mt-1">Cash out your earnings directly to your personal crypto wallet.</p>
    </div>

    <!-- Available Balance Card -->
    <div class="stat-card rounded-2xl p-5 mb-8 flex items-center justify-between border border-sky-500/20 bg-gradient-to-r from-sky-500/10 to-transparent">
        <div>
            <span class="text-xs font-medium text-zinc-400 uppercase tracking-wider">Available for Withdrawal</span>
            <div class="text-2xl font-black text-white mt-0.5"><?= Crypto::formatMoney($balance) ?></div>
        </div>
        <div class="text-right">
            <span class="text-xs text-zinc-400 block">Min. Withdrawal</span>
            <span class="text-xs font-bold text-sky-400"><?= Crypto::formatMoney($minWithdrawal) ?></span>
        </div>
    </div>

    <!-- Withdrawal Form -->
    <div class="stat-card rounded-2xl p-6 sm:p-8">
        <form action="withdraw" method="POST" @submit="loading = true" class="space-y-5">
            <?= Csrf::field() ?>

            <!-- Crypto Asset Type -->
            <div>
                <label for="crypto_type" class="block text-xs font-medium text-zinc-400 mb-1.5 uppercase tracking-wider">Select Payout Asset</label>
                <select id="crypto_type" name="crypto_type"
                    class="w-full px-4 py-2.5 bg-zinc-900/80 border border-white/10 rounded-xl text-sm text-zinc-100 focus:outline-none focus:border-sky-500 transition-all">
                    <option value="USDT-TRC20">USDT (TRC-20 Network - Recommended)</option>
                    <option value="USDT-ERC20">USDT (ERC-20 Network)</option>
                    <option value="BTC">Bitcoin (BTC)</option>
                </select>
            </div>

            <!-- Destination Address -->
            <div>
                <label for="crypto_address" class="block text-xs font-medium text-zinc-400 mb-1.5 uppercase tracking-wider">Your Destination Wallet Address</label>
                <input type="text" id="crypto_address" name="crypto_address" required
                    placeholder="Enter your USDT/BTC receiving address"
                    class="w-full px-4 py-2.5 bg-zinc-900/80 border border-white/10 rounded-xl text-sm font-mono text-zinc-100 placeholder-zinc-500 focus:outline-none focus:border-sky-500 transition-all">
                <p class="text-[11px] text-zinc-500 mt-1">Please double check your address carefully. Withdrawals sent to incorrect addresses cannot be retrieved.</p>
            </div>

            <!-- Amount -->
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label for="amount" class="block text-xs font-medium text-zinc-400 uppercase tracking-wider">Withdrawal Amount ($)</label>
                    <button type="button" @click="amount = balance" class="text-xs text-sky-400 hover:text-sky-300 font-semibold">Max Available</button>
                </div>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-zinc-500 font-bold">$</span>
                    <input type="number" id="amount" name="amount" x-model="amount" step="0.01" min="<?= $minWithdrawal ?>" max="<?= $balance ?>" required
                        placeholder="<?= number_format($minWithdrawal, 2) ?>"
                        class="w-full pl-8 pr-4 py-2.5 bg-zinc-900/80 border border-white/10 rounded-xl text-sm text-zinc-100 placeholder-zinc-500 focus:outline-none focus:border-sky-500 transition-all">
                </div>
            </div>

            <!-- Fee Calculation Summary -->
            <div class="p-4 rounded-xl bg-zinc-900/90 border border-white/5 space-y-2 text-xs">
                <div class="flex items-center justify-between text-zinc-400">
                    <span>Withdrawal Amount:</span>
                    <span class="font-medium text-zinc-200" x-text="'$' + (parseFloat(amount) || 0).toFixed(2)"></span>
                </div>
                <div class="flex items-center justify-between text-zinc-400">
                    <span>Processing Network Fee (<?= $feePercent ?>%):</span>
                    <span class="text-rose-400" x-text="'-$' + fee"></span>
                </div>
                <div class="pt-2 border-t border-white/10 flex items-center justify-between font-bold text-white">
                    <span>Estimated Net Payout:</span>
                    <span class="text-emerald-400 text-sm font-black" x-text="'$' + net"></span>
                </div>
            </div>

            <!-- Submit Button -->
            <button type="submit" :disabled="loading || parseFloat(amount) > balance || parseFloat(amount) < minWithdrawal"
                class="btn-brand w-full py-3 px-4 rounded-xl text-sm font-semibold text-white shadow-lg cursor-pointer flex items-center justify-center gap-2 mt-4 disabled:opacity-50 disabled:cursor-not-allowed">
                <span x-show="!loading">Request Payout</span>
                <span x-show="loading" class="flex items-center gap-2" x-cloak>Processing Request...</span>
                <i data-lucide="arrow-up-right" class="w-4 h-4" x-show="!loading"></i>
            </button>
        </form>
    </div>
</div>

<?php
$slot = ob_get_clean();
require __DIR__ . '/../layouts/app.php';
?>
