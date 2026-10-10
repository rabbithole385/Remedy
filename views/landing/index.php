<?php
$pageTitle = "Remedy Investment — High-Yield Micro-Tasks & Crypto Platform";
?>
<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <meta name="description" content="Earn crypto rewards daily by completing micro-tasks, surveys, and watching videos. Instant USDT & BTC withdrawals.">
    <meta name="theme-color" content="#0ea5e9">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: {
                        brand: {
                            DEFAULT: '#0ea5e9',
                            400: '#38bdf8',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                        }
                    }
                }
            }
        }
    </script>

    <!-- Inter Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script>
        (function () {
            var saved = localStorage.getItem('ud_theme');
            var isDark = saved !== null ? saved === 'dark' : true;
            if (isDark) document.documentElement.classList.add('dark');
            else document.documentElement.classList.remove('dark');
        })();
    </script>

    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Inter', sans-serif; }
        
        .orb {
            position: absolute; border-radius: 50%; filter: blur(90px); opacity: 0.15;
            animation: float 9s ease-in-out infinite;
        }
        .orb-1 { width: 500px; height: 500px; background: #0ea5e9; top: -150px; left: -150px; animation-delay: 0s; }
        .orb-2 { width: 400px; height: 400px; background: #38bdf8; top: 40%; right: -100px; animation-delay: -3s; }
        .orb-3 { width: 350px; height: 350px; background: #0284c7; bottom: -100px; left: 30%; animation-delay: -6s; }

        @keyframes float {
            0%, 100% { transform: translateY(0px) scale(1); }
            50% { transform: translateY(-25px) scale(1.05); }
        }

        .card-glass {
            background: rgba(24, 24, 27, 0.65);
            backdrop-filter: blur(28px);
            -webkit-backdrop-filter: blur(28px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
        html:not(.dark) .card-glass {
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid rgba(0, 0, 0, 0.08);
        }

        .btn-brand {
            background: linear-gradient(135deg, #0ea5e9, #0284c7);
            transition: all 0.2s ease;
        }
        .btn-brand:hover {
            background: linear-gradient(135deg, #38bdf8, #0ea5e9);
            transform: translateY(-2px);
            box-shadow: 0 12px 30px rgba(14, 165, 233, 0.4);
        }

        .badge-glow {
            box-shadow: 0 0 15px rgba(14, 165, 233, 0.35);
        }
    </style>
</head>
<body class="bg-zinc-950 text-zinc-100 min-h-screen flex flex-col justify-between overflow-x-hidden antialiased relative" x-data="{ mobileNav: false }">

    <!-- Ambient floating glow orbs -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none z-0">
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
        <div class="orb orb-3"></div>
    </div>

    <!-- Navigation Bar -->
    <header class="sticky top-0 z-50 bg-zinc-900/70 backdrop-blur-xl border-b border-white/5">
        <div class="max-w-7xl mx-auto px-4 sm:px-8 h-20 flex items-center justify-between">
            <!-- Brand Logo -->
            <a href="./" class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-sky-600 to-sky-400 flex items-center justify-center shadow-lg shadow-sky-500/20">
                    <i data-lucide="trending-up" class="w-6 h-6 text-white"></i>
                </div>
                <span class="font-extrabold text-lg tracking-tight text-white"><?= APP_NAME ?></span>
            </a>

            <!-- Desktop Navigation Links -->
            <nav class="hidden md:flex items-center gap-8 text-xs font-semibold text-zinc-400">
                <a href="#how-it-works" class="hover:text-sky-400 transition-colors">How It Works</a>
                <a href="#features" class="hover:text-sky-400 transition-colors">Features</a>
                <a href="#payments" class="hover:text-sky-400 transition-colors">Crypto Payments</a>
                <a href="#referrals" class="hover:text-sky-400 transition-colors">Affiliate 5%</a>
            </nav>

            <!-- Actions Right -->
            <div class="hidden md:flex items-center gap-4">
                <!-- Theme Toggle -->
                <div x-data="{
                    dark: document.documentElement.classList.contains('dark'),
                    toggle() {
                        this.dark = !this.dark;
                        if (this.dark) {
                            document.documentElement.classList.add('dark');
                            localStorage.setItem('ud_theme', 'dark');
                        } else {
                            document.documentElement.classList.remove('dark');
                            localStorage.setItem('ud_theme', 'light');
                        }
                    }
                }">
                    <button @click="toggle()" class="p-2.5 rounded-xl bg-zinc-800/60 hover:bg-zinc-800 text-zinc-300 transition-colors border border-white/5">
                        <template x-if="dark"><i data-lucide="sun" class="w-4 h-4"></i></template>
                        <template x-if="!dark"><i data-lucide="moon" class="w-4 h-4"></i></template>
                    </button>
                </div>

                <?php if (Auth::check()): ?>
                    <a href="dashboard" class="btn-brand px-5 py-2.5 rounded-xl text-xs font-bold text-white shadow-lg flex items-center gap-2">
                        <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
                        <span>Go to Dashboard</span>
                    </a>
                <?php else: ?>
                    <a href="login" class="px-4 py-2.5 rounded-xl text-xs font-semibold text-zinc-300 hover:text-white bg-white/5 hover:bg-white/10 transition-all border border-white/5">
                        Sign In
                    </a>
                    <a href="register" class="btn-brand px-5 py-2.5 rounded-xl text-xs font-bold text-white shadow-lg flex items-center gap-1.5">
                        <span>Get Started</span>
                        <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </a>
                <?php endif; ?>
            </div>

            <!-- Mobile Hamburger Button -->
            <button @click="mobileNav = !mobileNav" class="md:hidden p-2 rounded-xl bg-zinc-800 text-zinc-300">
                <i data-lucide="menu" class="w-6 h-6"></i>
            </button>
        </div>

        <!-- Mobile Drawer -->
        <div x-show="mobileNav" x-cloak x-transition class="md:hidden p-4 bg-zinc-900 border-b border-white/10 space-y-3">
            <a href="#how-it-works" @click="mobileNav = false" class="block text-sm font-medium text-zinc-300 py-1">How It Works</a>
            <a href="#features" @click="mobileNav = false" class="block text-sm font-medium text-zinc-300 py-1">Features</a>
            <a href="#payments" @click="mobileNav = false" class="block text-sm font-medium text-zinc-300 py-1">Crypto Payments</a>
            <a href="#referrals" @click="mobileNav = false" class="block text-sm font-medium text-zinc-300 py-1">Affiliate 5%</a>
            <div class="pt-3 border-t border-white/10 flex flex-col gap-2">
                <?php if (Auth::check()): ?>
                    <a href="dashboard" class="btn-brand text-center py-2.5 rounded-xl text-xs font-bold text-white">Go to Dashboard</a>
                <?php else: ?>
                    <a href="login" class="text-center py-2.5 rounded-xl text-xs font-semibold bg-zinc-800 text-white">Sign In</a>
                    <a href="register" class="btn-brand text-center py-2.5 rounded-xl text-xs font-bold text-white">Create Account</a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <!-- Main Content Area -->
    <main class="relative z-10 flex-1">
        
        <!-- Hero Section -->
        <section class="pt-16 pb-20 px-4 sm:px-8 max-w-7xl mx-auto text-center">
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-sky-500/15 border border-sky-500/30 text-sky-400 text-xs font-bold mb-6 badge-glow">
                <i data-lucide="sparkles" class="w-4 h-4"></i>
                High-Yield Smart Earning Network 2.0
            </div>

            <h1 class="text-4xl sm:text-6xl lg:text-7xl font-black text-white tracking-tight max-w-4xl mx-auto leading-tight mb-6">
                Turn Daily Tasks Into <span class="bg-gradient-to-r from-sky-400 via-cyan-300 to-blue-500 bg-clip-text text-transparent">Crypto Yields</span>
            </h1>

            <p class="text-base sm:text-lg text-zinc-400 max-w-2xl mx-auto mb-10 leading-relaxed">
                Complete reviews, surveys, order checks, and media missions. Receive verified payouts directly to your USDT (TRC20/ERC20) or Bitcoin wallet.
            </p>

            <!-- Hero Action Buttons -->
            <div class="flex flex-wrap items-center justify-center gap-4">
                <a href="register" class="btn-brand px-8 py-4 rounded-2xl text-base font-bold text-white shadow-2xl flex items-center gap-3">
                    <span>Start Earning Now</span>
                    <i data-lucide="arrow-right" class="w-5 h-5"></i>
                </a>
                <a href="login" class="px-8 py-4 rounded-2xl text-base font-semibold text-zinc-300 bg-zinc-900/80 hover:bg-zinc-800 transition-all border border-white/10 flex items-center gap-2">
                    <i data-lucide="log-in" class="w-5 h-5 text-sky-400"></i>
                    <span>Member Login</span>
                </a>
            </div>

            <!-- Stats Bar -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 max-w-5xl mx-auto mt-16 pt-10 border-t border-white/10">
                <div class="card-glass rounded-2xl p-5">
                    <div class="text-2xl sm:text-3xl font-black text-white">$1,480,250+</div>
                    <div class="text-xs text-zinc-400 mt-1 uppercase font-semibold">Total Paid Out</div>
                </div>
                <div class="card-glass rounded-2xl p-5">
                    <div class="text-2xl sm:text-3xl font-black text-sky-400">48,500+</div>
                    <div class="text-xs text-zinc-400 mt-1 uppercase font-semibold">Active Earners</div>
                </div>
                <div class="card-glass rounded-2xl p-5">
                    <div class="text-2xl sm:text-3xl font-black text-emerald-400">250,000+</div>
                    <div class="text-xs text-zinc-400 mt-1 uppercase font-semibold">Missions Completed</div>
                </div>
                <div class="card-glass rounded-2xl p-5">
                    <div class="text-2xl sm:text-3xl font-black text-purple-400">&lt; 15 Mins</div>
                    <div class="text-xs text-zinc-400 mt-1 uppercase font-semibold">Average Payout Time</div>
                </div>
            </div>
        </section>

        <!-- How It Works Section -->
        <section id="how-it-works" class="py-20 px-4 sm:px-8 max-w-7xl mx-auto">
            <div class="text-center mb-16">
                <span class="text-xs font-bold uppercase tracking-wider text-sky-400">Simple 3-Step Process</span>
                <h2 class="text-3xl sm:text-4xl font-black text-white tracking-tight mt-2">How Remedy Investment Works</h2>
                <p class="text-sm text-zinc-400 mt-2 max-w-xl mx-auto">No prior experience needed. Start earning in less than 2 minutes.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Step 1 -->
                <div class="card-glass rounded-3xl p-8 relative flex flex-col justify-between border-t-2 border-t-sky-500">
                    <div>
                        <div class="w-14 h-14 rounded-2xl bg-sky-500/10 border border-sky-500/20 flex items-center justify-center text-sky-400 text-xl font-black mb-6">
                            01
                        </div>
                        <h3 class="text-xl font-bold text-white mb-3">Create Free Account</h3>
                        <p class="text-sm text-zinc-400 leading-relaxed">
                            Sign up instantly with basic information. Get immediate access to your personalized earning dashboard.
                        </p>
                    </div>
                    <div class="mt-8 flex items-center gap-2 text-xs font-bold text-sky-400">
                        <i data-lucide="user-plus" class="w-4 h-4"></i>
                        Instant Registration
                    </div>
                </div>

                <!-- Step 2 -->
                <div class="card-glass rounded-3xl p-8 relative flex flex-col justify-between border-t-2 border-t-cyan-500">
                    <div>
                        <div class="w-14 h-14 rounded-2xl bg-cyan-500/10 border border-cyan-500/20 flex items-center justify-center text-cyan-400 text-xl font-black mb-6">
                            02
                        </div>
                        <h3 class="text-xl font-bold text-white mb-3">Complete Micro-Missions</h3>
                        <p class="text-sm text-zinc-400 leading-relaxed">
                            Browse daily tasks: rate products, complete quick orders, fill out feedback surveys, or watch media.
                        </p>
                    </div>
                    <div class="mt-8 flex items-center gap-2 text-xs font-bold text-cyan-400">
                        <i data-lucide="check-square" class="w-4 h-4"></i>
                        Submit Easy Proofs
                    </div>
                </div>

                <!-- Step 3 -->
                <div class="card-glass rounded-3xl p-8 relative flex flex-col justify-between border-t-2 border-t-emerald-500">
                    <div>
                        <div class="w-14 h-14 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400 text-xl font-black mb-6">
                            03
                        </div>
                        <h3 class="text-xl font-bold text-white mb-3">Instant Crypto Payouts</h3>
                        <p class="text-sm text-zinc-400 leading-relaxed">
                            Request your verified earnings directly to your USDT (TRC20/ERC20) or Bitcoin wallet address.
                        </p>
                    </div>
                    <div class="mt-8 flex items-center gap-2 text-xs font-bold text-emerald-400">
                        <i data-lucide="wallet" class="w-4 h-4"></i>
                        Automated Verification
                    </div>
                </div>
            </div>
        </section>

        <!-- Supported Crypto Assets -->
        <section id="payments" class="py-16 px-4 sm:px-8 max-w-7xl mx-auto">
            <div class="card-glass rounded-3xl p-8 sm:p-12 border border-sky-500/20 bg-gradient-to-r from-sky-950/40 via-zinc-900 to-zinc-950">
                <div class="text-center mb-10">
                    <span class="text-xs font-bold uppercase tracking-wider text-emerald-400">Supported Blockchains</span>
                    <h2 class="text-2xl sm:text-3xl font-black text-white mt-1">Multi-Asset Crypto Deposits & Withdrawals</h2>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                    <div class="p-6 rounded-2xl bg-zinc-900/90 border border-white/10 text-center">
                        <div class="w-12 h-12 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center mx-auto mb-3 text-emerald-400 font-black">
                            TRC20
                        </div>
                        <h4 class="text-base font-bold text-white">USDT (TRC-20)</h4>
                        <p class="text-xs text-zinc-400 mt-1">Tron Network · Ultra-low transaction fees</p>
                    </div>

                    <div class="p-6 rounded-2xl bg-zinc-900/90 border border-white/10 text-center">
                        <div class="w-12 h-12 rounded-xl bg-blue-500/10 border border-blue-500/20 flex items-center justify-center mx-auto mb-3 text-blue-400 font-black">
                            ERC20
                        </div>
                        <h4 class="text-base font-bold text-white">USDT (ERC-20)</h4>
                        <p class="text-xs text-zinc-400 mt-1">Ethereum Mainnet · High liquidity support</p>
                    </div>

                    <div class="p-6 rounded-2xl bg-zinc-900/90 border border-white/10 text-center">
                        <div class="w-12 h-12 rounded-xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center mx-auto mb-3 text-amber-400 font-black">
                            BTC
                        </div>
                        <h4 class="text-base font-bold text-white">Bitcoin (BTC)</h4>
                        <p class="text-xs text-zinc-400 mt-1">Native Bitcoin · Secured ledger verification</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Affiliate Section -->
        <section id="referrals" class="py-16 px-4 sm:px-8 max-w-7xl mx-auto">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-purple-400">Passive Income Program</span>
                    <h2 class="text-3xl sm:text-4xl font-black text-white tracking-tight mt-2 mb-4">
                        Earn 5% Lifetime Commission on Referrals
                    </h2>
                    <p class="text-sm text-zinc-400 leading-relaxed mb-6">
                        Invite your friends, colleagues, or social media audience using your personal referral link. Receive 5% instant commission whenever your referred members make their initial deposit.
                    </p>
                    <div class="space-y-3 text-xs text-zinc-300 mb-8">
                        <div class="flex items-center gap-3">
                            <div class="w-6 h-6 rounded-full bg-purple-500/20 flex items-center justify-center text-purple-400 font-bold">✓</div>
                            <span>Unique shareable link & referral code generated instantly</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-6 h-6 rounded-full bg-purple-500/20 flex items-center justify-center text-purple-400 font-bold">✓</div>
                            <span>Real-time downline reporting and commission tracking</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-6 h-6 rounded-full bg-purple-500/20 flex items-center justify-center text-purple-400 font-bold">✓</div>
                            <span>No caps or limits on referral earnings</span>
                        </div>
                    </div>
                    <a href="register" class="btn-brand px-6 py-3.5 rounded-xl text-xs font-bold text-white inline-flex items-center gap-2">
                        <span>Join Referral Program</span>
                        <i data-lucide="gift" class="w-4 h-4"></i>
                    </a>
                </div>

                <div class="card-glass rounded-3xl p-8 border border-purple-500/20">
                    <div class="flex items-center justify-between mb-6 pb-4 border-b border-white/10">
                        <span class="text-xs font-bold uppercase text-zinc-400">Referral Earnings Preview</span>
                        <span class="px-2.5 py-1 rounded bg-purple-500/20 text-purple-300 text-xs font-bold">5% Bonus Rate</span>
                    </div>

                    <div class="space-y-4 text-xs">
                        <div class="p-4 rounded-xl bg-white/5 flex items-center justify-between">
                            <span class="text-zinc-400">Referee Deposits $100</span>
                            <span class="font-bold text-emerald-400">You Receive +$5.00</span>
                        </div>
                        <div class="p-4 rounded-xl bg-white/5 flex items-center justify-between">
                            <span class="text-zinc-400">Referee Deposits $500</span>
                            <span class="font-bold text-emerald-400">You Receive +$25.00</span>
                        </div>
                        <div class="p-4 rounded-xl bg-white/5 flex items-center justify-between">
                            <span class="text-zinc-400">Referee Deposits $2,000</span>
                            <span class="font-bold text-emerald-400">You Receive +$100.00</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="py-20 px-4 sm:px-8 max-w-5xl mx-auto text-center">
            <div class="card-glass rounded-3xl p-10 sm:p-14 border border-sky-500/30 relative overflow-hidden">
                <h2 class="text-3xl sm:text-5xl font-black text-white tracking-tight mb-4">
                    Ready to Start Earning?
                </h2>
                <p class="text-sm sm:text-base text-zinc-400 max-w-lg mx-auto mb-8">
                    Join thousands of satisfied earners worldwide on <?= APP_NAME ?> today.
                </p>
                <a href="register" class="btn-brand px-10 py-4 rounded-2xl text-base font-bold text-white shadow-2xl inline-flex items-center gap-2">
                    <span>Create Your Account</span>
                    <i data-lucide="arrow-right" class="w-5 h-5"></i>
                </a>
            </div>
        </section>

    </main>

    <!-- Footer -->
    <footer class="relative z-10 py-10 px-4 sm:px-8 border-t border-white/5 bg-zinc-900/50">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-center justify-between gap-6 text-xs text-zinc-500">
            <div class="flex items-center gap-3">
                <div class="w-7 h-7 rounded-xl bg-sky-500/20 flex items-center justify-center text-sky-400 font-bold">
                    <i data-lucide="trending-up" class="w-4 h-4"></i>
                </div>
                <span class="font-bold text-white text-sm"><?= APP_NAME ?></span>
            </div>

            <div class="flex items-center gap-6">
                <a href="login" class="hover:text-zinc-300">Sign In</a>
                <a href="register" class="hover:text-zinc-300">Register</a>
                <a href="forgot-password" class="hover:text-zinc-300">Recovery</a>
            </div>

            <div class="text-center md:text-right">
                <p>&copy; <?= date('Y') ?> <?= APP_NAME ?>. All rights reserved.</p>
                <p class="mt-1 text-[11px] text-zinc-600">SSL Encrypted Connection · System Online</p>
            </div>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            lucide.createIcons();
        });
    </script>
</body>
</html>
