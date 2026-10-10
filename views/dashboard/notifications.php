<?php
$pageTitle = "Notifications";
$currentRoute = "notifications";
ob_start();
?>

<!-- Header -->
<div class="flex items-center justify-between mb-8">
    <div>
        <h1 class="text-2xl font-black text-white tracking-tight">Notifications & Alerts</h1>
        <p class="text-sm text-zinc-400 mt-1">Updates on your task submissions, deposits, and withdrawal approvals.</p>
    </div>
</div>

<!-- Notifications List -->
<div class="stat-card rounded-2xl p-6">
    <div class="space-y-3">
        <?php if (empty($notifications)): ?>
            <div class="py-12 text-center text-zinc-500">
                <i data-lucide="bell-off" class="w-10 h-10 mx-auto mb-2 text-zinc-600"></i>
                <p class="text-sm font-semibold text-white">No notifications found</p>
                <p class="text-xs text-zinc-400 mt-0.5">You're all caught up!</p>
            </div>
        <?php else: foreach ($notifications as $n): ?>
            <div class="p-4 rounded-xl border transition-all flex items-start gap-4
                <?= match($n['type']) {
                    'success' => 'bg-emerald-500/5 border-emerald-500/20',
                    'error'   => 'bg-rose-500/5 border-rose-500/20',
                    'warning' => 'bg-amber-500/5 border-amber-500/20',
                    default   => 'bg-white/5 border-white/5',
                } ?>">
                <div class="mt-0.5 w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0
                    <?= match($n['type']) {
                        'success' => 'bg-emerald-500/20 text-emerald-400',
                        'error'   => 'bg-rose-500/20 text-rose-400',
                        'warning' => 'bg-amber-500/20 text-amber-400',
                        default   => 'bg-sky-500/20 text-sky-400',
                    } ?>">
                    <i data-lucide="<?= match($n['type']) {
                        'success' => 'check-circle',
                        'error'   => 'alert-circle',
                        'warning' => 'alert-triangle',
                        default   => 'info',
                    } ?>" class="w-4 h-4"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between gap-2">
                        <h4 class="text-xs font-bold text-white"><?= htmlspecialchars($n['title']) ?></h4>
                        <span class="text-[10px] text-zinc-500 flex-shrink-0"><?= date('M d, H:i', strtotime($n['created_at'])) ?></span>
                    </div>
                    <p class="text-xs text-zinc-300 mt-1 leading-relaxed"><?= htmlspecialchars($n['message']) ?></p>
                    <?php if (!empty($n['link'])): ?>
                        <a href="<?= htmlspecialchars($n['link']) ?>" class="inline-flex items-center gap-1 text-[11px] text-sky-400 hover:underline mt-2">
                            <span>View Details</span>
                            <i data-lucide="chevron-right" class="w-3 h-3"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; endif; ?>
    </div>
</div>

<?php
$slot = ob_get_clean();
require __DIR__ . '/../layouts/app.php';
?>
