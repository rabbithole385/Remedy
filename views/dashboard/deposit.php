<?php
$pageTitle = "Deposit Crypto";
$currentRoute = "deposit";
ob_start();
?>

<div class="max-w-3xl mx-auto" x-data="{
    coin: 'USDT-TRC20',
    wallets: {
        'USDT-TRC20': '<?= htmlspecialchars($wallets['USDT-TRC20']) ?>',
        'USDT-ERC20': '<?= htmlspecialchars($wallets['USDT-ERC20']) ?>',
        'BTC': '<?= htmlspecialchars($wallets['BTC']) ?>'
    },
    copied: false,
    copyAddress() {
        navigator.clipboard.writeText(this.wallets[this.coin]);
        this.copied = true;
        setTimeout(() => this.copied = false, 2000);
    }
}">

    <!-- Header -->
    <div class="mb-8 text-center sm:text-left">
        <h1 class="text-2xl font-black text-white tracking-tight">Deposit Cryptocurrency</h1>
        <p class="text-sm text-zinc-400 mt-1">Transfer funds to the official wallet address below and submit your transaction hash for instant verification.</p>
    </div>

    <!-- Step 1: Choose Currency & Wallet -->
    <div class="stat-card rounded-2xl p-6 sm:p-8 mb-8">
        <span class="text-xs font-bold uppercase tracking-wider text-sky-400 mb-3 block">Step 1 — Choose Network & Payment Asset</span>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-6">
            <button type="button" @click="coin = 'USDT-TRC20'"
                :class="coin === 'USDT-TRC20' ? 'border-sky-500 bg-sky-500/10 text-white' : 'border-white/10 bg-zinc-900/60 text-zinc-400'"
                class="p-4 rounded-xl border text-left transition-all">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold">USDT</span>
                    <span class="text-[10px] px-1.5 py-0.5 rounded bg-emerald-500/20 text-emerald-300 font-bold">TRC-20</span>
                </div>
                <p class="text-[11px] text-zinc-400">Tron Network (Fastest, low fee)</p>
            </button>

            <button type="button" @click="coin = 'USDT-ERC20'"
                :class="coin === 'USDT-ERC20' ? 'border-sky-500 bg-sky-500/10 text-white' : 'border-white/10 bg-zinc-900/60 text-zinc-400'"
                class="p-4 rounded-xl border text-left transition-all">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold">USDT</span>
                    <span class="text-[10px] px-1.5 py-0.5 rounded bg-blue-500/20 text-blue-300 font-bold">ERC-20</span>
                </div>
                <p class="text-[11px] text-zinc-400">Ethereum Mainnet</p>
            </button>

            <button type="button" @click="coin = 'BTC'"
                :class="coin === 'BTC' ? 'border-sky-500 bg-sky-500/10 text-white' : 'border-white/10 bg-zinc-900/60 text-zinc-400'"
                class="p-4 rounded-xl border text-left transition-all">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold">Bitcoin</span>
                    <span class="text-[10px] px-1.5 py-0.5 rounded bg-amber-500/20 text-amber-300 font-bold">BTC</span>
                </div>
                <p class="text-[11px] text-zinc-400">Bitcoin Native Network</p>
            </button>
        </div>

        <!-- Official Wallet Display Box -->
        <div class="p-4 rounded-xl bg-zinc-900/90 border border-white/10">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[11px] text-zinc-400">Deposit Address for <span class="text-sky-400 font-bold" x-text="coin"></span>:</span>
                <span class="text-[11px] text-emerald-400 font-medium">Network Confirmed</span>
            </div>
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
                <input type="text" readonly :value="wallets[coin]"
                    class="flex-1 px-3.5 py-2.5 bg-black/40 border border-white/10 rounded-xl font-mono text-xs text-sky-300 select-all focus:outline-none">
                <button type="button" @click="copyAddress()"
                    class="px-4 py-2.5 rounded-xl bg-sky-500 hover:bg-sky-400 text-white text-xs font-semibold transition-colors flex items-center justify-center gap-1.5 shadow-md">
                    <i data-lucide="copy" class="w-4 h-4"></i>
                    <span x-text="copied ? 'Copied!' : 'Copy Address'"></span>
                </button>
            </div>
            <p class="text-[11px] text-amber-400/90 mt-3 flex items-center gap-1.5">
                <i data-lucide="alert-triangle" class="w-3.5 h-3.5 flex-shrink-0"></i>
                Only send funds using the selected network. Sending other assets will cause permanent loss.
            </p>
        </div>
    </div>

    <!-- Step 2: Submit Proof Form -->
    <div class="stat-card rounded-2xl p-6 sm:p-8" x-data="{ loading: false }">
        <span class="text-xs font-bold uppercase tracking-wider text-sky-400 mb-3 block">Step 2 — Submit Proof of Transfer</span>

        <form action="deposit" method="POST" enctype="multipart/form-data" @submit="loading = true" class="space-y-5">
            <?= Csrf::field() ?>
            <input type="hidden" name="crypto_type" :value="coin">

            <!-- Amount -->
            <div>
                <label for="amount" class="block text-xs font-medium text-zinc-400 mb-1.5 uppercase tracking-wider">Deposit Amount (USD Equiv.)</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-zinc-500 font-bold">$</span>
                    <input type="number" id="amount" name="amount" step="0.01" min="<?= $minDeposit ?>" required
                        placeholder="<?= number_format($minDeposit, 2) ?>"
                        class="w-full pl-8 pr-4 py-2.5 bg-zinc-900/80 border border-white/10 rounded-xl text-sm text-zinc-100 placeholder-zinc-500 focus:outline-none focus:border-sky-500 transition-all">
                </div>
                <p class="text-[11px] text-zinc-500 mt-1">Minimum deposit is <?= Crypto::formatMoney($minDeposit) ?></p>
            </div>

            <!-- Transaction Hash -->
            <div>
                <label for="tx_hash" class="block text-xs font-medium text-zinc-400 mb-1.5 uppercase tracking-wider">Transaction Hash / TXID</label>
                <input type="text" id="tx_hash" name="tx_hash" required
                    placeholder="e.g. 5b7d6051785fe7f..."
                    class="w-full px-4 py-2.5 bg-zinc-900/80 border border-white/10 rounded-xl text-sm font-mono text-zinc-100 placeholder-zinc-500 focus:outline-none focus:border-sky-500 transition-all">
            </div>

            <!-- Upload Receipt / Screenshot -->
            <div>
                <label class="block text-xs font-medium text-zinc-400 mb-1.5 uppercase tracking-wider">Upload Transfer Screenshot (Optional)</label>
                <div class="relative border-2 border-dashed border-white/15 rounded-2xl p-5 text-center hover:border-sky-500/50 transition-colors bg-zinc-900/40">
                    <input type="file" name="proof_file" accept="image/jpeg,image/png,image/webp"
                        class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                    <i data-lucide="upload" class="w-8 h-8 text-sky-400 mx-auto mb-2"></i>
                    <p class="text-xs font-medium text-white">Click or drag screenshot here</p>
                    <p class="text-[10px] text-zinc-500 mt-0.5">PNG, JPG or WebP up to 5MB</p>
                </div>
            </div>

            <!-- Submit Button -->
            <button type="submit" :disabled="loading"
                class="btn-brand w-full py-3 px-4 rounded-xl text-sm font-semibold text-white shadow-lg cursor-pointer flex items-center justify-center gap-2 mt-4">
                <span x-show="!loading">Submit Deposit For Verification</span>
                <span x-show="loading" class="flex items-center gap-2" x-cloak>Submitting Transfer...</span>
                <i data-lucide="check-circle" class="w-4 h-4" x-show="!loading"></i>
            </button>
        </form>
    </div>
</div>

<?php
$slot = ob_get_clean();
require __DIR__ . '/../layouts/app.php';
?>
