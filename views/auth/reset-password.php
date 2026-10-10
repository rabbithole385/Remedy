<?php
$pageTitle = "Set New Password | " . APP_NAME;
?>
<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <meta name="theme-color" content="#0ea5e9">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: {
                        brand: { DEFAULT: '#0ea5e9', 400: '#38bdf8', 500: '#0ea5e9', 600: '#0284c7', 700: '#0369a1' }
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Inter', sans-serif; }
        .card-glass {
            background: rgba(24, 24, 27, 0.65);
            backdrop-filter: blur(24px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
        .btn-brand {
            background: linear-gradient(135deg, #0ea5e9, #0284c7);
        }
    </style>
</head>
<body class="bg-zinc-950 text-zinc-100 min-h-screen flex items-center justify-center p-4 relative antialiased">
    <div class="w-full max-w-md relative z-10 my-8">
        <div class="text-center mb-6">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-gradient-to-tr from-sky-600 to-sky-400 shadow-xl shadow-sky-500/20 mb-3">
                <i data-lucide="shield-check" class="w-7 h-7 text-white"></i>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-white">New Password</h1>
            <p class="text-sm text-zinc-400 mt-1">Set a secure password for your account</p>
        </div>

        <?= Flash::render() ?>

        <div class="card-glass rounded-2xl p-8 shadow-2xl relative" x-data="{ loading: false }">
            <form action="reset-password" method="POST" @submit="loading = true" class="space-y-4">
                <?= Csrf::field() ?>
                <input type="hidden" name="token" value="<?= htmlspecialchars($token ?? '') ?>">

                <div>
                    <label for="password" class="block text-xs font-medium text-zinc-400 mb-1.5 uppercase tracking-wider">New Password</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-zinc-500">
                            <i data-lucide="lock" class="w-4 h-4"></i>
                        </span>
                        <input type="password" id="password" name="password" required minlength="6"
                            placeholder="••••••••••••"
                            class="w-full pl-10 pr-4 py-2.5 bg-zinc-900/80 border border-white/10 rounded-xl text-sm text-zinc-100 placeholder-zinc-500 focus:outline-none focus:border-sky-500 transition-all">
                    </div>
                </div>

                <div>
                    <label for="password_confirmation" class="block text-xs font-medium text-zinc-400 mb-1.5 uppercase tracking-wider">Confirm New Password</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-zinc-500">
                            <i data-lucide="check-check" class="w-4 h-4"></i>
                        </span>
                        <input type="password" id="password_confirmation" name="password_confirmation" required minlength="6"
                            placeholder="••••••••••••"
                            class="w-full pl-10 pr-4 py-2.5 bg-zinc-900/80 border border-white/10 rounded-xl text-sm text-zinc-100 placeholder-zinc-500 focus:outline-none focus:border-sky-500 transition-all">
                    </div>
                </div>

                <button type="submit" :disabled="loading"
                    class="btn-brand w-full py-3 px-4 rounded-xl text-sm font-semibold text-white shadow-lg cursor-pointer flex items-center justify-center gap-2 mt-2">
                    <span x-show="!loading">Update Password</span>
                    <span x-show="loading" class="flex items-center gap-2" x-cloak>Saving...</span>
                    <i data-lucide="check" class="w-4 h-4" x-show="!loading"></i>
                </button>
            </form>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            lucide.createIcons();
        });
    </script>
</body>
</html>
