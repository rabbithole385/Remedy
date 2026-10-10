<?php
$pageTitle = "Sign In | " . APP_NAME;
?>
<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <meta name="description" content="Sign in to your <?= APP_NAME ?> account">
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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
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
            position: absolute; border-radius: 50%; filter: blur(80px); opacity: 0.12;
            animation: float 8s ease-in-out infinite;
        }
        .orb-1 { width: 400px; height: 400px; background: #0ea5e9; top: -100px; left: -100px; animation-delay: 0s; }
        .orb-2 { width: 300px; height: 300px; background: #38bdf8; bottom: -50px; right: -50px; animation-delay: -3s; }
        .orb-3 { width: 200px; height: 200px; background: #0284c7; top: 50%; left: 50%; animation-delay: -6s; }

        @keyframes float {
            0%, 100% { transform: translateY(0px) scale(1); }
            50% { transform: translateY(-20px) scale(1.05); }
        }
        .input-glow:focus { box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.15); }
        .card-glass {
            background: rgba(24, 24, 27, 0.6);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid rgba(255, 255, 255, 0.07);
        }
        html:not(.dark) .card-glass {
            background: rgba(255, 255, 255, 0.85);
            border: 1px solid rgba(0, 0, 0, 0.08);
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
    </style>
</head>
<body class="bg-zinc-950 dark:bg-zinc-950 text-zinc-100 min-h-screen flex items-center justify-center p-4 relative overflow-hidden antialiased">
    <!-- Ambient floating glow orbs -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none z-0">
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
        <div class="orb orb-3"></div>
    </div>

    <!-- Theme toggle -->
    <div class="absolute top-5 right-5 z-20" x-data="{
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
        <button @click="toggle()" class="p-2.5 rounded-xl bg-zinc-900/60 border border-white/5 text-zinc-400 hover:text-zinc-200 transition-colors backdrop-blur-md">
            <template x-if="dark"><i data-lucide="sun" class="w-4 h-4"></i></template>
            <template x-if="!dark"><i data-lucide="moon" class="w-4 h-4"></i></template>
        </button>
    </div>

    <!-- Main Container -->
    <div class="w-full max-w-md relative z-10 my-8">
        <!-- Logo & Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-gradient-to-tr from-sky-600 to-sky-400 shadow-xl shadow-sky-500/20 mb-4">
                <i data-lucide="trending-up" class="w-7 h-7 text-white"></i>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-white"><?= APP_NAME ?></h1>
            <p class="text-sm text-zinc-400 mt-1">Sign in to manage your tasks & earnings</p>
        </div>

        <?= Flash::render() ?>

        <!-- Sign-in card -->
        <div class="card-glass rounded-2xl p-8 shadow-2xl relative" x-data="{ showPass: false, loading: false }">
            <form action="login" method="POST" @submit="loading = true" class="space-y-5">
                <?= Csrf::field() ?>

                <!-- Email / Username -->
                <div>
                    <label for="identity" class="block text-xs font-medium text-zinc-400 mb-1.5 uppercase tracking-wider">Username or Email</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-zinc-500">
                            <i data-lucide="user" class="w-4 h-4"></i>
                        </span>
                        <input type="text" id="identity" name="identity" required autofocus autocomplete="username"
                            placeholder="admin@remedy.com"
                            class="w-full pl-10 pr-4 py-2.5 bg-zinc-900/80 border border-white/10 rounded-xl text-sm text-zinc-100 placeholder-zinc-500 focus:outline-none focus:border-sky-500 input-glow transition-all">
                    </div>
                </div>

                <!-- Password -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="password" class="block text-xs font-medium text-zinc-400 uppercase tracking-wider">Password</label>
                        <a href="forgot-password" class="text-xs text-sky-400 hover:text-sky-300 transition-colors">Forgot password?</a>
                    </div>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-zinc-500">
                            <i data-lucide="lock" class="w-4 h-4"></i>
                        </span>
                        <input :type="showPass ? 'text' : 'password'" id="password" name="password" required autocomplete="current-password"
                            placeholder="••••••••••••"
                            class="w-full pl-10 pr-10 py-2.5 bg-zinc-900/80 border border-white/10 rounded-xl text-sm text-zinc-100 placeholder-zinc-500 focus:outline-none focus:border-sky-500 input-glow transition-all">
                        <button type="button" @click="showPass = !showPass" class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-zinc-500 hover:text-zinc-300 transition-colors">
                            <template x-if="!showPass"><i data-lucide="eye" class="w-4 h-4"></i></template>
                            <template x-if="showPass"><i data-lucide="eye-off" class="w-4 h-4"></i></template>
                        </button>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" :disabled="loading"
                    class="btn-brand w-full py-3 px-4 rounded-xl text-sm font-semibold text-white shadow-lg cursor-pointer flex items-center justify-center gap-2 mt-2">
                    <span x-show="!loading">Sign In</span>
                    <span x-show="loading" class="flex items-center gap-2" x-cloak>
                        <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        Authenticating...
                    </span>
                    <i data-lucide="arrow-right" class="w-4 h-4" x-show="!loading"></i>
                </button>
            </form>

            <!-- Registration link -->
            <div class="mt-6 pt-6 border-t border-white/5 text-center">
                <p class="text-xs text-zinc-400">
                    Don't have an account?
                    <a href="register" class="font-medium text-sky-400 hover:text-sky-300 transition-colors ml-1">Create one here</a>
                </p>
            </div>
        </div>

        <!-- Footer -->
        <p class="text-center text-xs text-zinc-500 mt-6">
            &copy; <?= date('Y') ?> <?= APP_NAME ?>. Bank-grade SSL encryption.
        </p>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            lucide.createIcons();
        });
    </script>
</body>
</html>
