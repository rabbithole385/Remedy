<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>404 — Page Not Found | <?= APP_NAME ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
    <style>body { font-family: 'Inter', sans-serif; background: #09090b; color: #f4f4f5; }</style>
</head>
<body class="min-h-screen flex items-center justify-center p-4 text-center">
    <div class="max-w-md w-full p-8 rounded-3xl bg-zinc-900/60 border border-white/5 backdrop-blur-xl">
        <div class="w-16 h-16 rounded-2xl bg-sky-500/10 border border-sky-500/20 flex items-center justify-center mx-auto mb-4 text-sky-400">
            <i data-lucide="compass" class="w-8 h-8"></i>
        </div>
        <h1 class="text-4xl font-black text-white mb-2">404</h1>
        <h2 class="text-lg font-bold text-zinc-200 mb-2">Page Not Found</h2>
        <p class="text-xs text-zinc-400 mb-6">The page or resource you requested does not exist or has been moved.</p>
        <a href="dashboard" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-sky-500 hover:bg-sky-400 text-white text-xs font-semibold transition-colors">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Return to Dashboard
        </a>
    </div>
    <script>lucide.createIcons();</script>
</body>
</html>
