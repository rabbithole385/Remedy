<?php
$pageTitle = "Submit Task Proof";
$currentRoute = "tasks";
ob_start();
?>

<div class="max-w-3xl mx-auto">
    <!-- Back Button -->
    <a href="tasks" class="inline-flex items-center gap-2 text-xs font-medium text-zinc-400 hover:text-white transition-colors mb-6">
        <i data-lucide="arrow-left" class="w-4 h-4"></i>
        Back to Task List
    </a>

    <!-- Task Details Card -->
    <div class="stat-card rounded-2xl p-6 sm:p-8 mb-8">
        <div class="flex items-center justify-between mb-4">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold uppercase tracking-wider bg-sky-500/10 text-sky-400 border border-sky-500/20">
                <?= htmlspecialchars($task['type']) ?> Mission
            </span>
            <span class="text-xl font-black text-emerald-400">
                Reward: <?= Crypto::formatMoney($task['reward']) ?>
            </span>
        </div>

        <h1 class="text-2xl font-black text-white mb-3"><?= htmlspecialchars($task['title']) ?></h1>
        <p class="text-sm text-zinc-300 leading-relaxed mb-6">
            <?= nl2br(htmlspecialchars($task['description'])) ?>
        </p>

        <!-- Instructions Box -->
        <?php if (!empty($task['instructions'])): ?>
            <div class="p-4 rounded-xl bg-sky-500/5 border border-sky-500/15 mb-6">
                <h4 class="text-xs font-bold uppercase tracking-wider text-sky-400 mb-2 flex items-center gap-1.5">
                    <i data-lucide="info" class="w-4 h-4"></i>
                    Step-by-step Instructions:
                </h4>
                <div class="text-xs text-zinc-300 space-y-1.5 leading-relaxed">
                    <?= nl2br(htmlspecialchars($task['instructions'])) ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if (!empty($task['target_url'])): ?>
            <div class="mb-6">
                <a href="<?= htmlspecialchars($task['target_url']) ?>" target="_blank" rel="noopener noreferrer"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-zinc-800 hover:bg-zinc-700 text-xs font-semibold text-white transition-colors border border-white/10">
                    <i data-lucide="external-link" class="w-4 h-4 text-sky-400"></i>
                    <span>Open Task Target URL in New Tab</span>
                </a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Submission Form -->
    <div class="stat-card rounded-2xl p-6 sm:p-8" x-data="{ proofType: 'screenshot', isUploading: false }">
        <h3 class="text-lg font-bold text-white mb-2">Submit Your Verification Proof</h3>
        <p class="text-xs text-zinc-400 mb-6">Upload clear proof of completion so our review team can approve your reward quickly.</p>

        <form action="tasks/submit" method="POST" enctype="multipart/form-data" @submit="isUploading = true" class="space-y-6">
            <?= Csrf::field() ?>
            <input type="hidden" name="task_id" value="<?= $task['id'] ?>">

            <!-- Proof Type Selector -->
            <div>
                <label class="block text-xs font-medium text-zinc-400 mb-2 uppercase tracking-wider">Proof Format</label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="cursor-pointer">
                        <input type="radio" name="proof_type" value="screenshot" x-model="proofType" class="sr-only">
                        <div :class="proofType === 'screenshot' ? 'bg-sky-500/15 border-sky-500 text-sky-300' : 'bg-zinc-900/60 border-white/10 text-zinc-400'"
                            class="p-3.5 rounded-xl border flex items-center justify-center gap-2 text-xs font-semibold transition-all">
                            <i data-lucide="image" class="w-4 h-4"></i>
                            <span>Screenshot Image</span>
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="proof_type" value="url" x-model="proofType" class="sr-only">
                        <div :class="proofType === 'url' ? 'bg-sky-500/15 border-sky-500 text-sky-300' : 'bg-zinc-900/60 border-white/10 text-zinc-400'"
                            class="p-3.5 rounded-xl border flex items-center justify-center gap-2 text-xs font-semibold transition-all">
                            <i data-lucide="link" class="w-4 h-4"></i>
                            <span>URL Link or Text</span>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Screenshot File Upload -->
            <div x-show="proofType === 'screenshot'">
                <label class="block text-xs font-medium text-zinc-400 mb-2 uppercase tracking-wider">Screenshot File (JPEG, PNG, WebP up to 5MB)</label>
                <div class="relative border-2 border-dashed border-white/15 rounded-2xl p-6 text-center hover:border-sky-500/50 transition-colors bg-zinc-900/40">
                    <input type="file" name="proof_file" accept="image/jpeg,image/png,image/webp" :required="proofType === 'screenshot'"
                        class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                    <i data-lucide="upload-cloud" class="w-10 h-10 text-sky-400 mx-auto mb-2"></i>
                    <p class="text-xs font-medium text-white">Click or drag & drop to choose screenshot</p>
                    <p class="text-[11px] text-zinc-500 mt-1">PNG, JPG or WebP up to 5MB</p>
                </div>
            </div>

            <!-- URL / Text Proof -->
            <div x-show="proofType === 'url'" x-cloak>
                <label for="proof_text" class="block text-xs font-medium text-zinc-400 mb-2 uppercase tracking-wider">Verification Link or Confirmation Code</label>
                <input type="text" id="proof_text" name="proof_text" :required="proofType === 'url'"
                    placeholder="https://... or confirmation code"
                    class="w-full px-4 py-2.5 bg-zinc-900/80 border border-white/10 rounded-xl text-sm text-zinc-100 placeholder-zinc-500 focus:outline-none focus:border-sky-500">
            </div>

            <!-- Optional Notes -->
            <div>
                <label for="notes" class="block text-xs font-medium text-zinc-400 mb-2 uppercase tracking-wider">Additional Notes (Optional)</label>
                <textarea id="notes" name="notes" rows="3"
                    placeholder="Any extra details for the administrator..."
                    class="w-full px-4 py-2.5 bg-zinc-900/80 border border-white/10 rounded-xl text-sm text-zinc-100 placeholder-zinc-500 focus:outline-none focus:border-sky-500"></textarea>
            </div>

            <!-- Submit Button -->
            <button type="submit" :disabled="isUploading"
                class="btn-brand w-full py-3 px-4 rounded-xl text-sm font-semibold text-white shadow-lg cursor-pointer flex items-center justify-center gap-2">
                <span x-show="!isUploading">Submit Proof For Review</span>
                <span x-show="isUploading" class="flex items-center gap-2" x-cloak>Uploading & Submitting...</span>
                <i data-lucide="send" class="w-4 h-4" x-show="!isUploading"></i>
            </button>
        </form>
    </div>
</div>

<?php
$slot = ob_get_clean();
require __DIR__ . '/../layouts/app.php';
?>
