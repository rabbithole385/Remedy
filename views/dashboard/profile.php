<?php
$pageTitle = "Settings & Security";
$currentRoute = "profile";
ob_start();
?>

<!-- Header -->
<div class="mb-8">
    <h1 class="text-2xl font-black text-white tracking-tight">Account Profile & Security</h1>
    <p class="text-sm text-zinc-400 mt-1">Update your personal details, profile image and manage account security.</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- User Overview Card -->
    <div class="stat-card rounded-2xl p-6 flex flex-col items-center text-center">
        <div class="w-24 h-24 rounded-full bg-sky-500/20 border-2 border-sky-500/40 flex items-center justify-center text-2xl font-bold text-sky-400 mb-4 overflow-hidden relative group">
            <?php if (!empty($user['avatar'])): ?>
                <img src="<?= htmlspecialchars($user['avatar']) ?>" alt="Avatar" class="w-full h-full object-cover">
            <?php else: ?>
                <?= strtoupper(substr($user['username'], 0, 2)) ?>
            <?php endif; ?>
        </div>
        <h3 class="text-lg font-bold text-white"><?= htmlspecialchars($user['username']) ?></h3>
        <p class="text-xs text-zinc-400 mt-0.5"><?= htmlspecialchars($user['email']) ?></p>

        <div class="w-full mt-6 pt-6 border-t border-white/5 space-y-3 text-left text-xs">
            <div class="flex items-center justify-between">
                <span class="text-zinc-500">Account Role</span>
                <span class="font-semibold text-white uppercase"><?= htmlspecialchars($user['role']) ?></span>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-zinc-500">Referral Code</span>
                <span class="font-mono font-bold text-sky-400"><?= htmlspecialchars($user['referral_code']) ?></span>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-zinc-500">Joined</span>
                <span class="text-zinc-300"><?= date('M d, Y', strtotime($user['created_at'])) ?></span>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-zinc-500">Last Login</span>
                <span class="text-zinc-300"><?= !empty($user['last_login_at']) ? date('M d, H:i', strtotime($user['last_login_at'])) : 'Recent' ?></span>
            </div>
        </div>
    </div>

    <!-- Forms Column -->
    <div class="lg:col-span-2 space-y-8">
        <!-- Edit Profile Form -->
        <div class="stat-card rounded-2xl p-6 sm:p-8">
            <h3 class="text-base font-bold text-white mb-2">Personal Information</h3>
            <p class="text-xs text-zinc-400 mb-6">Update contact info and display avatar</p>

            <form action="profile/update" method="POST" enctype="multipart/form-data" class="space-y-4">
                <?= Csrf::field() ?>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="first_name" class="block text-xs font-medium text-zinc-400 mb-1.5 uppercase tracking-wider">First Name</label>
                        <input type="text" id="first_name" name="first_name" value="<?= htmlspecialchars($user['first_name'] ?? '') ?>"
                            placeholder="John"
                            class="w-full px-4 py-2.5 bg-zinc-900/80 border border-white/10 rounded-xl text-sm text-zinc-100 focus:outline-none focus:border-sky-500">
                    </div>
                    <div>
                        <label for="last_name" class="block text-xs font-medium text-zinc-400 mb-1.5 uppercase tracking-wider">Last Name</label>
                        <input type="text" id="last_name" name="last_name" value="<?= htmlspecialchars($user['last_name'] ?? '') ?>"
                            placeholder="Doe"
                            class="w-full px-4 py-2.5 bg-zinc-900/80 border border-white/10 rounded-xl text-sm text-zinc-100 focus:outline-none focus:border-sky-500">
                    </div>
                </div>

                <div>
                    <label for="phone" class="block text-xs font-medium text-zinc-400 mb-1.5 uppercase tracking-wider">Phone Number</label>
                    <input type="text" id="phone" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>"
                        placeholder="+1 555 019 283"
                        class="w-full px-4 py-2.5 bg-zinc-900/80 border border-white/10 rounded-xl text-sm text-zinc-100 focus:outline-none focus:border-sky-500">
                </div>

                <div>
                    <label for="avatar" class="block text-xs font-medium text-zinc-400 mb-1.5 uppercase tracking-wider">Change Avatar Photo</label>
                    <input type="file" id="avatar" name="avatar" accept="image/jpeg,image/png,image/webp"
                        class="w-full text-xs text-zinc-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-sky-500/10 file:text-sky-400 hover:file:bg-sky-500/20 cursor-pointer">
                </div>

                <div class="pt-2">
                    <button type="submit" class="btn-brand px-5 py-2.5 rounded-xl text-xs font-semibold text-white shadow-md">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>

        <!-- Change Password Form -->
        <div class="stat-card rounded-2xl p-6 sm:p-8">
            <h3 class="text-base font-bold text-white mb-2">Change Password</h3>
            <p class="text-xs text-zinc-400 mb-6">Ensure your account uses a strong passphrase</p>

            <form action="profile/password" method="POST" class="space-y-4">
                <?= Csrf::field() ?>

                <div>
                    <label for="current_password" class="block text-xs font-medium text-zinc-400 mb-1.5 uppercase tracking-wider">Current Password</label>
                    <input type="password" id="current_password" name="current_password" required
                        class="w-full px-4 py-2.5 bg-zinc-900/80 border border-white/10 rounded-xl text-sm text-zinc-100 focus:outline-none focus:border-sky-500">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="new_password" class="block text-xs font-medium text-zinc-400 mb-1.5 uppercase tracking-wider">New Password</label>
                        <input type="password" id="new_password" name="new_password" required minlength="6"
                            class="w-full px-4 py-2.5 bg-zinc-900/80 border border-white/10 rounded-xl text-sm text-zinc-100 focus:outline-none focus:border-sky-500">
                    </div>
                    <div>
                        <label for="new_password_confirmation" class="block text-xs font-medium text-zinc-400 mb-1.5 uppercase tracking-wider">Confirm New Password</label>
                        <input type="password" id="new_password_confirmation" name="new_password_confirmation" required minlength="6"
                            class="w-full px-4 py-2.5 bg-zinc-900/80 border border-white/10 rounded-xl text-sm text-zinc-100 focus:outline-none focus:border-sky-500">
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-zinc-800 hover:bg-zinc-700 text-white text-xs font-semibold border border-white/10 transition-colors">
                        Update Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
$slot = ob_get_clean();
require __DIR__ . '/../layouts/app.php';
?>
