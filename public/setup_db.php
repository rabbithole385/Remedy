<?php
// ============================================================
// Remedy Investment — Database Setup & Diagnostics Page
// ============================================================

$error = null;
$success = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'install') {
    $host = $_POST['host'] ?? DB_HOST;
    $port = $_POST['port'] ?? DB_PORT;
    $user = $_POST['user'] ?? DB_USER;
    $pass = $_POST['pass'] ?? DB_PASS;
    $dbname = $_POST['dbname'] ?? DB_NAME;

    try {
        $rootPdo = new PDO("mysql:host={$host};port={$port}", $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);

        $schemaSql = file_get_contents(__DIR__ . '/../database/schema.sql');
        $rootPdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbname}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
        $rootPdo->exec("USE `{$dbname}`;");
        $rootPdo->exec($schemaSql);

        $success = "Database '{$dbname}' and all tables were created successfully! You can now log in.";
    } catch (Exception $e) {
        $error = "Database setup error: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Database Setup | <?= APP_NAME ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
    <style>
        body { font-family: 'Inter', sans-serif; background: #09090b; color: #f4f4f5; }
        .card-glass {
            background: rgba(24, 24, 27, 0.7);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-lg card-glass rounded-3xl p-8 shadow-2xl">
        <div class="text-center mb-6">
            <div class="w-14 h-14 rounded-2xl bg-amber-500/20 border border-amber-500/30 flex items-center justify-center mx-auto mb-3 text-amber-400">
                <i data-lucide="database" class="w-7 h-7"></i>
            </div>
            <h1 class="text-2xl font-black text-white">Database Configuration</h1>
            <p class="text-xs text-zinc-400 mt-1">Connect your MySQL database or run the automated installer</p>
        </div>

        <?php if ($success): ?>
            <div class="p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-xs mb-6">
                <?= htmlspecialchars($success) ?>
                <div class="mt-3">
                    <a href="login" class="px-4 py-2 bg-emerald-500 text-black font-bold rounded-xl inline-block text-xs">Proceed to Sign In &rarr;</a>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="p-4 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-400 text-xs mb-6">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <div class="p-4 rounded-xl bg-zinc-900 border border-white/5 text-xs text-zinc-300 space-y-2 mb-6">
            <div class="flex justify-between">
                <span class="text-zinc-500">Configured Host:</span>
                <span class="font-mono text-white"><?= DB_HOST ?>:<?= DB_PORT ?></span>
            </div>
            <div class="flex justify-between">
                <span class="text-zinc-500">Database Name:</span>
                <span class="font-mono text-white"><?= DB_NAME ?></span>
            </div>
            <div class="flex justify-between">
                <span class="text-zinc-500">Config Path:</span>
                <span class="font-mono text-sky-400">src/config/config.php</span>
            </div>
        </div>

        <form method="POST" class="space-y-4">
            <input type="hidden" name="action" value="install">
            <div>
                <label class="block text-xs font-semibold text-zinc-400 mb-1">MySQL User</label>
                <input type="text" name="user" value="<?= DB_USER ?>" required class="w-full px-4 py-2 bg-zinc-900 border border-white/10 rounded-xl text-xs text-white">
            </div>
            <div>
                <label class="block text-xs font-semibold text-zinc-400 mb-1">MySQL Password</label>
                <input type="password" name="pass" value="<?= DB_PASS ?>" placeholder="Leave blank if none" class="w-full px-4 py-2 bg-zinc-900 border border-white/10 rounded-xl text-xs text-white">
            </div>
            <div>
                <label class="block text-xs font-semibold text-zinc-400 mb-1">Database Name to Create</label>
                <input type="text" name="dbname" value="<?= DB_NAME ?>" required class="w-full px-4 py-2 bg-zinc-900 border border-white/10 rounded-xl text-xs text-white">
            </div>

            <button type="submit" class="w-full py-3 rounded-xl bg-amber-500 hover:bg-amber-400 text-black text-xs font-bold transition-colors">
                Run Automated Database Setup
            </button>
        </form>

        <div class="mt-6 pt-6 border-t border-white/5 text-center text-xs text-zinc-500">
            Default Admin credentials after setup:<br>
            <span class="text-zinc-300 font-mono">admin@remedy.com</span> / <span class="text-zinc-300 font-mono">Admin@1234</span>
        </div>
    </div>
    <script>lucide.createIcons();</script>
</body>
</html>
