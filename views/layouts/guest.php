<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($pageTitle ?? 'Remedy Investment') ?></title>
    <meta name="description" content="Remedy Investment — High-Yield Smart Earning & Task Platform">
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
            position: absolute;
            border-radius: 50%;
            filter: blur(90px);
            opacity: 0.14;
            animation: float 9s ease-in-out infinite;
        }
        .orb-1 { width: 450px; height: 450px; background: #0ea5e9; top: -120px; left: -120px; animation-delay: 0s; }
        .orb-2 { width: 350px; height: 350px; background: #38bdf8; bottom: -80px; right: -80px; animation-delay: -3s; }
        .orb-3 { width: 250px; height: 250px; background: #0284c7; top: 40%; left: 45%; animation-delay: -6s; }

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
            transform: translateY(-1px);
            box-shadow: 0 8px 25px rgba(14, 165, 233, 0.35);
        }
    </style>
</head>
<body class="bg-zinc-950 text-zinc-100 min-h-screen flex flex-col justify-between overflow-x-hidden antialiased">
    <!-- Ambient floating glow orbs -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none z-0">
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
        <div class="orb orb-3"></div>
    </div>

    <!-- Main Content Slot -->
    <main class="relative z-10 flex-1 flex items-center justify-center p-4 py-12">
        <div class="w-full max-w-md">
            <?= Flash::render() ?>
            <?php if (isset($viewContent)) include $viewContent; ?>
        </div>
    </main>

    <!-- Guest Footer -->
    <footer class="relative z-10 py-6 text-center text-xs text-zinc-500 border-t border-white/5">
        <p>&copy; <?= date('Y') ?> <?= APP_NAME ?>. All rights reserved. Secure encrypted connection.</p>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            lucide.createIcons();
        });
    </script>
</body>
</html>
