<?php
$user = $user ?? Auth::user();
$currentRoute = $currentRoute ?? '';
$unreadCount = Notification::getUnreadCount($user['id']);
?>
<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($pageTitle ?? 'Dashboard') ?> | <?= APP_NAME ?></title>
    <meta name="description" content="<?= APP_NAME ?> — Smart Task & Crypto Platform">
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
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

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
        * { font-family: 'Inter', sans-serif; }

        /* Scrollbars */
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(113, 113, 122, 0.25); border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: rgba(14, 165, 233, 0.4); }

        .stat-card {
            background: rgba(24, 24, 27, 0.65);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.07);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 35px rgba(0, 0, 0, 0.35);
        }
        html:not(.dark) .stat-card {
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid rgba(0, 0, 0, 0.08);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        }

        .nav-link {
            transition: all 0.15s ease;
            border-radius: 12px;
        }
        .nav-link:hover {
            background: rgba(14, 165, 233, 0.08);
            color: #38bdf8;
        }
        .nav-link.active {
            background: linear-gradient(135deg, rgba(14, 165, 233, 0.18), rgba(2, 132, 199, 0.1));
            color: #38bdf8;
            border-left: 3px solid #0ea5e9;
            font-weight: 600;
        }

        .btn-brand {
            background: linear-gradient(135deg, #0ea5e9, #0284c7);
            transition: all 0.2s ease;
        }
        .btn-brand:hover {
            background: linear-gradient(135deg, #38bdf8, #0ea5e9);
            transform: translateY(-1px);
            box-shadow: 0 8px 25px rgba(14, 165, 233, 0.35);
        }

        .badge-glow {
            box-shadow: 0 0 10px rgba(14, 165, 233, 0.4);
        }
    </style>
</head>
<body class="bg-zinc-950 text-zinc-100 min-h-screen flex antialiased" x-data="{ mobileMenu: false }">

    <!-- Mobile Sidebar Backdrop -->
    <div x-show="mobileMenu" @click="mobileMenu = false" x-transition.opacity class="fixed inset-0 bg-black/60 backdrop-blur-sm z-40 lg:hidden" x-cloak></div>

    <!-- Sidebar Navigation -->
    <aside :class="mobileMenu ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
        class="fixed lg:sticky top-0 left-0 h-screen w-64 bg-zinc-900/80 backdrop-blur-2xl border-r border-white/5 flex flex-col justify-between z-50 transition-transform duration-300 ease-in-out">
        
        <!-- Sidebar Header -->
        <div>
            <div class="h-16 flex items-center justify-between px-6 border-b border-white/5">
                <a href="dashboard" class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-sky-600 to-sky-400 flex items-center justify-center shadow-lg shadow-sky-500/20">
                        <i data-lucide="trending-up" class="w-5 h-5 text-white"></i>
                    </div>
                    <span class="font-bold text-base tracking-tight text-white"><?= APP_NAME ?></span>
                </a>
                <button @click="mobileMenu = false" class="lg:hidden text-zinc-400 hover:text-white p-1">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <!-- User Quick Card in Sidebar -->
            <div class="p-4 mx-4 my-4 rounded-xl bg-gradient-to-br from-sky-500/10 to-transparent border border-sky-500/20">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-sky-500/20 border border-sky-500/30 flex items-center justify-center text-sky-400 font-bold overflow-hidden">
                        <?php if (!empty($user['avatar'])): ?>
                            <img src="<?= htmlspecialchars($user['avatar']) ?>" alt="Avatar" class="w-full h-full object-cover">
                        <?php else: ?>
                            <?= strtoupper(substr($user['username'], 0, 2)) ?>
                        <?php endif; ?>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs text-zinc-400">Total Balance</p>
                        <p class="text-base font-bold text-white truncate"><?= Crypto::formatMoney($user['balance']) ?></p>
                    </div>
                </div>
            </div>

            <!-- Navigation Links -->
            <nav class="px-4 space-y-1.5 overflow-y-auto max-h-[calc(100vh-270px)]">
                <a href="dashboard" class="nav-link flex items-center gap-3 px-3.5 py-2.5 text-sm font-medium text-zinc-400 <?= $currentRoute === 'dashboard' ? 'active' : '' ?>">
                    <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
                    <span>Dashboard</span>
                </a>
                <a href="tasks" class="nav-link flex items-center gap-3 px-3.5 py-2.5 text-sm font-medium text-zinc-400 <?= $currentRoute === 'tasks' ? 'active' : '' ?>">
                    <i data-lucide="check-square" class="w-4 h-4"></i>
                    <span>Available Tasks</span>
                </a>
                <a href="wallet" class="nav-link flex items-center gap-3 px-3.5 py-2.5 text-sm font-medium text-zinc-400 <?= $currentRoute === 'wallet' ? 'active' : '' ?>">
                    <i data-lucide="wallet" class="w-4 h-4"></i>
                    <span>My Wallet</span>
                </a>
                <a href="deposit" class="nav-link flex items-center gap-3 px-3.5 py-2.5 text-sm font-medium text-zinc-400 <?= $currentRoute === 'deposit' ? 'active' : '' ?>">
                    <i data-lucide="arrow-down-left" class="w-4 h-4 text-emerald-400"></i>
                    <span>Deposit Crypto</span>
                </a>
                <a href="withdraw" class="nav-link flex items-center gap-3 px-3.5 py-2.5 text-sm font-medium text-zinc-400 <?= $currentRoute === 'withdraw' ? 'active' : '' ?>">
                    <i data-lucide="arrow-up-right" class="w-4 h-4 text-sky-400"></i>
                    <span>Withdraw Funds</span>
                </a>
                <a href="referral" class="nav-link flex items-center gap-3 px-3.5 py-2.5 text-sm font-medium text-zinc-400 <?= $currentRoute === 'referral' ? 'active' : '' ?>">
                    <i data-lucide="users" class="w-4 h-4"></i>
                    <span>Referral Program</span>
                </a>
                <a href="transactions" class="nav-link flex items-center gap-3 px-3.5 py-2.5 text-sm font-medium text-zinc-400 <?= $currentRoute === 'transactions' ? 'active' : '' ?>">
                    <i data-lucide="history" class="w-4 h-4"></i>
                    <span>Transactions</span>
                </a>
                <a href="profile" class="nav-link flex items-center gap-3 px-3.5 py-2.5 text-sm font-medium text-zinc-400 <?= $currentRoute === 'profile' ? 'active' : '' ?>">
                    <i data-lucide="settings" class="w-4 h-4"></i>
                    <span>Settings & Security</span>
                </a>

                <?php if (Auth::isAdmin()): ?>
                <div class="pt-3 mt-3 border-t border-white/5">
                    <p class="px-3.5 pb-2 text-[10px] font-bold uppercase tracking-wider text-amber-400">Admin Section</p>
                    <a href="admin" class="nav-link flex items-center gap-3 px-3.5 py-2.5 text-sm font-medium text-amber-400/90 hover:text-amber-300">
                        <i data-lucide="shield-alert" class="w-4 h-4"></i>
                        <span>Admin Console</span>
                    </a>
                </div>
                <?php endif; ?>
            </nav>
        </div>

        <!-- Sidebar Footer -->
        <div class="p-4 border-t border-white/5">
            <a href="logout" class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-sm font-medium text-rose-400 hover:bg-rose-500/10 transition-colors">
                <i data-lucide="log-out" class="w-4 h-4"></i>
                <span>Sign Out</span>
            </a>
        </div>
    </aside>

    <!-- Main Viewport -->
    <div class="flex-1 flex flex-col min-w-0">
        <!-- Top Navigation Header -->
        <header class="h-16 bg-zinc-900/60 backdrop-blur-xl border-b border-white/5 sticky top-0 z-30 px-4 sm:px-8 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <button @click="mobileMenu = true" class="lg:hidden p-2 rounded-lg bg-zinc-800/80 text-zinc-400 hover:text-white">
                    <i data-lucide="menu" class="w-5 h-5"></i>
                </button>
                <h2 class="text-lg font-semibold text-white tracking-tight hidden sm:block"><?= htmlspecialchars($pageTitle ?? 'Dashboard') ?></h2>
            </div>

            <!-- Topbar Actions -->
            <div class="flex items-center gap-3 sm:gap-4">
                <!-- Balance Chip -->
                <div class="hidden sm:flex items-center gap-2 px-3 py-1.5 rounded-full bg-zinc-800/70 border border-white/10 text-xs">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span class="text-zinc-400">Balance:</span>
                    <span class="font-bold text-white"><?= Crypto::formatMoney($user['balance']) ?></span>
                </div>

                <!-- Deposit Fast Action -->
                <a href="deposit" class="hidden md:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-sky-500/15 border border-sky-500/30 text-sky-400 text-xs font-semibold hover:bg-sky-500/25 transition-all">
                    <i data-lucide="plus-circle" class="w-3.5 h-3.5"></i>
                    <span>Deposit</span>
                </a>

                <!-- Notifications Bell -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="relative p-2 rounded-xl bg-zinc-800/60 hover:bg-zinc-800 text-zinc-300 transition-colors">
                        <i data-lucide="bell" class="w-4 h-4"></i>
                        <?php if ($unreadCount > 0): ?>
                            <span class="absolute top-1 right-1 w-2 h-2 bg-sky-500 rounded-full animate-ping"></span>
                            <span class="absolute top-1 right-1 w-2 h-2 bg-sky-500 rounded-full"></span>
                        <?php endif; ?>
                    </button>

                    <!-- Dropdown -->
                    <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-80 rounded-2xl card-glass shadow-2xl p-4 z-50 border border-white/10" x-cloak>
                        <div class="flex items-center justify-between pb-3 border-b border-white/10 mb-2">
                            <span class="text-xs font-bold uppercase tracking-wider text-white">Notifications</span>
                            <a href="notifications" class="text-xs text-sky-400 hover:text-sky-300">View all</a>
                        </div>
                        <div class="space-y-2 text-xs">
                            <?php 
                            $topNotifs = Notification::getByUser($user['id'], 3);
                            if (empty($topNotifs)): 
                            ?>
                                <p class="text-zinc-500 text-center py-4">No notifications yet</p>
                            <?php else: foreach ($topNotifs as $n): ?>
                                <div class="p-2.5 rounded-xl bg-white/5 border border-white/5">
                                    <p class="font-semibold text-zinc-200"><?= htmlspecialchars($n['title']) ?></p>
                                    <p class="text-zinc-400 text-[11px] mt-0.5 line-clamp-2"><?= htmlspecialchars($n['message']) ?></p>
                                </div>
                            <?php endforeach; endif; ?>
                        </div>
                    </div>
                </div>

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
                    <button @click="toggle()" class="p-2 rounded-xl bg-zinc-800/60 hover:bg-zinc-800 text-zinc-300 transition-colors">
                        <template x-if="dark"><i data-lucide="sun" class="w-4 h-4"></i></template>
                        <template x-if="!dark"><i data-lucide="moon" class="w-4 h-4"></i></template>
                    </button>
                </div>

                <!-- Profile Dropdown -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="flex items-center gap-2 p-1 pl-2 rounded-xl bg-zinc-800/60 hover:bg-zinc-800 text-zinc-300 transition-colors">
                        <span class="text-xs font-medium text-white hidden md:inline"><?= htmlspecialchars($user['username']) ?></span>
                        <div class="w-7 h-7 rounded-lg bg-sky-500/20 border border-sky-500/30 flex items-center justify-center text-xs font-bold text-sky-400 overflow-hidden">
                            <?php if (!empty($user['avatar'])): ?>
                                <img src="<?= htmlspecialchars($user['avatar']) ?>" alt="Avatar" class="w-full h-full object-cover">
                            <?php else: ?>
                                <?= strtoupper(substr($user['username'], 0, 1)) ?>
                            <?php endif; ?>
                        </div>
                    </button>

                    <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-48 rounded-2xl card-glass shadow-2xl p-2 z-50 border border-white/10" x-cloak>
                        <a href="profile" class="flex items-center gap-2 px-3 py-2 text-xs font-medium text-zinc-300 hover:text-white hover:bg-white/5 rounded-xl transition-colors">
                            <i data-lucide="user" class="w-3.5 h-3.5"></i>
                            My Profile
                        </a>
                        <a href="wallet" class="flex items-center gap-2 px-3 py-2 text-xs font-medium text-zinc-300 hover:text-white hover:bg-white/5 rounded-xl transition-colors">
                            <i data-lucide="wallet" class="w-3.5 h-3.5"></i>
                            Wallet Balance
                        </a>
                        <div class="my-1 border-t border-white/5"></div>
                        <a href="logout" class="flex items-center gap-2 px-3 py-2 text-xs font-medium text-rose-400 hover:bg-rose-500/10 rounded-xl transition-colors">
                            <i data-lucide="log-out" class="w-3.5 h-3.5"></i>
                            Sign Out
                        </a>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Workspace Area -->
        <main class="flex-1 p-4 sm:p-8 max-w-7xl w-full mx-auto">
            <?= Flash::render() ?>
            <?php 
            if (isset($slot)) {
                echo $slot;
            } elseif (isset($viewContent)) {
                include $viewContent;
            }
            ?>
        </main>

        <!-- Footer -->
        <footer class="py-6 px-8 border-t border-white/5 text-center sm:flex sm:justify-between text-xs text-zinc-500">
            <p>&copy; <?= date('Y') ?> <?= APP_NAME ?>. All rights reserved.</p>
            <p class="mt-1 sm:mt-0">Crypto Network Status: <span class="text-emerald-400 font-medium">Operational</span></p>
        </footer>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            lucide.createIcons();
        });
    </script>
</body>
</html>
