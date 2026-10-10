<?php
$user = Auth::user();
$currentRoute = $currentRoute ?? '';
?>
<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($pageTitle ?? 'Admin Console') ?> | <?= APP_NAME ?></title>
    <meta name="theme-color" content="#f59e0b">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: {
                        brand: { DEFAULT: '#0ea5e9', 400: '#38bdf8', 500: '#0ea5e9', 600: '#0284c7' }
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
        * { font-family: 'Inter', sans-serif; }
        .stat-card {
            background: rgba(24, 24, 27, 0.7);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
        .nav-link {
            transition: all 0.15s ease;
            border-radius: 12px;
        }
        .nav-link:hover {
            background: rgba(245, 158, 11, 0.1);
            color: #fbbf24;
        }
        .nav-link.active {
            background: linear-gradient(135deg, rgba(245, 158, 11, 0.2), rgba(217, 119, 6, 0.15));
            color: #fbbf24;
            border-left: 3px solid #f59e0b;
            font-weight: 600;
        }
    </style>
</head>
<body class="bg-zinc-950 text-zinc-100 min-h-screen flex antialiased" x-data="{ mobileMenu: false }">

    <!-- Mobile Backdrop -->
    <div x-show="mobileMenu" @click="mobileMenu = false" x-transition.opacity class="fixed inset-0 bg-black/60 backdrop-blur-sm z-40 lg:hidden" x-cloak></div>

    <!-- Admin Sidebar -->
    <aside :class="mobileMenu ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
        class="fixed lg:sticky top-0 left-0 h-screen w-64 bg-zinc-900/90 backdrop-blur-2xl border-r border-white/5 flex flex-col justify-between z-50 transition-transform duration-300 ease-in-out">
        
        <div>
            <!-- Header -->
            <div class="h-16 flex items-center justify-between px-6 border-b border-white/5">
                <a href="admin" class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-amber-500 to-amber-400 flex items-center justify-center shadow-lg shadow-amber-500/20">
                        <i data-lucide="shield" class="w-5 h-5 text-black"></i>
                    </div>
                    <div>
                        <span class="font-black text-sm tracking-tight text-white block">ADMIN PANEL</span>
                        <span class="text-[10px] text-amber-400 uppercase font-semibold">Master Console</span>
                    </div>
                </a>
                <button @click="mobileMenu = false" class="lg:hidden text-zinc-400 hover:text-white p-1">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <!-- Nav Links -->
            <nav class="p-4 space-y-1.5 overflow-y-auto max-h-[calc(100vh-140px)]">
                <a href="admin" class="nav-link flex items-center gap-3 px-3.5 py-2.5 text-xs font-medium text-zinc-400 <?= $currentRoute === 'admin_index' ? 'active' : '' ?>">
                    <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
                    <span>Overview</span>
                </a>
                <a href="admin/users" class="nav-link flex items-center gap-3 px-3.5 py-2.5 text-xs font-medium text-zinc-400 <?= $currentRoute === 'admin_users' ? 'active' : '' ?>">
                    <i data-lucide="users" class="w-4 h-4"></i>
                    <span>User Accounts</span>
                </a>
                <a href="admin/tasks" class="nav-link flex items-center gap-3 px-3.5 py-2.5 text-xs font-medium text-zinc-400 <?= $currentRoute === 'admin_tasks' ? 'active' : '' ?>">
                    <i data-lucide="list-todo" class="w-4 h-4"></i>
                    <span>Task Management</span>
                </a>
                <a href="admin/submissions" class="nav-link flex items-center gap-3 px-3.5 py-2.5 text-xs font-medium text-zinc-400 <?= $currentRoute === 'admin_submissions' ? 'active' : '' ?>">
                    <i data-lucide="check-check" class="w-4 h-4"></i>
                    <span>Task Submissions</span>
                </a>
                <a href="admin/deposits" class="nav-link flex items-center gap-3 px-3.5 py-2.5 text-xs font-medium text-zinc-400 <?= $currentRoute === 'admin_deposits' ? 'active' : '' ?>">
                    <i data-lucide="arrow-down-left" class="w-4 h-4 text-emerald-400"></i>
                    <span>Crypto Deposits</span>
                </a>
                <a href="admin/withdrawals" class="nav-link flex items-center gap-3 px-3.5 py-2.5 text-xs font-medium text-zinc-400 <?= $currentRoute === 'admin_withdrawals' ? 'active' : '' ?>">
                    <i data-lucide="arrow-up-right" class="w-4 h-4 text-rose-400"></i>
                    <span>Withdrawals</span>
                </a>
                <a href="admin/transactions" class="nav-link flex items-center gap-3 px-3.5 py-2.5 text-xs font-medium text-zinc-400 <?= $currentRoute === 'admin_transactions' ? 'active' : '' ?>">
                    <i data-lucide="receipt" class="w-4 h-4"></i>
                    <span>All Transactions</span>
                </a>
                <a href="admin/settings" class="nav-link flex items-center gap-3 px-3.5 py-2.5 text-xs font-medium text-zinc-400 <?= $currentRoute === 'admin_settings' ? 'active' : '' ?>">
                    <i data-lucide="sliders" class="w-4 h-4"></i>
                    <span>Wallets & Settings</span>
                </a>

                <div class="pt-3 mt-3 border-t border-white/5">
                    <a href="dashboard" class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-xs font-semibold text-sky-400 hover:bg-sky-500/10 transition-colors">
                        <i data-lucide="external-link" class="w-4 h-4"></i>
                        <span>Switch to User View</span>
                    </a>
                </div>
            </nav>
        </div>

        <div class="p-4 border-t border-white/5">
            <a href="logout" class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-xs font-medium text-rose-400 hover:bg-rose-500/10 transition-colors">
                <i data-lucide="log-out" class="w-4 h-4"></i>
                <span>Sign Out</span>
            </a>
        </div>
    </aside>

    <!-- Content Viewport -->
    <div class="flex-1 flex flex-col min-w-0">
        <header class="h-16 bg-zinc-900/60 backdrop-blur-xl border-b border-white/5 sticky top-0 z-30 px-4 sm:px-8 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <button @click="mobileMenu = true" class="lg:hidden p-2 rounded-lg bg-zinc-800 text-zinc-400 hover:text-white">
                    <i data-lucide="menu" class="w-5 h-5"></i>
                </button>
                <div class="flex items-center gap-2">
                    <span class="px-2 py-0.5 rounded bg-amber-500/20 text-amber-400 text-[10px] font-bold uppercase tracking-wider">SuperAdmin</span>
                    <h2 class="text-base font-bold text-white tracking-tight"><?= htmlspecialchars($pageTitle ?? 'Admin Console') ?></h2>
                </div>
            </div>
            <div>
                <a href="dashboard" class="text-xs font-semibold text-zinc-400 hover:text-white transition-colors">User Dashboard &rarr;</a>
            </div>
        </header>

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
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            lucide.createIcons();
        });
    </script>
</body>
</html>
