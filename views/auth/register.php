<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Create Account | Task Investment</title>
    <meta name="title" content="Create Account | Task Investment">
    <meta name="description" content="Create an account on Task Investment">
    <meta name="theme-color" content="#0ea5e9">
    <meta name="application-name" content="Task Investment">

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
    <!-- Alpine.js CDN -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>

<body class="bg-zinc-50 dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100 antialiased min-h-screen"
      x-data="authTheme()">

    <!-- HEADER BAR -->
    <header class="fixed top-0 inset-x-0 z-20 flex items-center justify-between px-5 py-3 bg-white/70 dark:bg-zinc-900/70 backdrop-blur-sm border-b border-zinc-200 dark:border-zinc-800">
        <!-- Logo + site name -->
        <a href="login" class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-xl bg-gradient-to-tr from-sky-600 to-sky-400 flex items-center justify-center text-white shadow-md">
                <i data-lucide="trending-up" class="w-5 h-5"></i>
            </div>
            <span class="font-bold text-sm text-zinc-900 dark:text-white">Task Investment</span>
        </a>

        <!-- Right: theme toggle + auth links -->
        <div class="flex items-center gap-2">
            <button @click="toggleTheme()"
                class="p-2 text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-lg transition-colors"
                :title="isDark ? 'Switch to light mode' : 'Switch to dark mode'">
                <i data-lucide="sun" class="w-4 h-4" x-show="isDark" x-cloak></i>
                <i data-lucide="moon" class="w-4 h-4" x-show="!isDark" x-cloak></i>
            </button>

            <a href="login"
               class="text-xs font-medium px-3 py-1.5 rounded-lg bg-sky-600 hover:bg-sky-500 text-white transition-colors">
                Sign In
            </a>
        </div>
    </header>

    <!-- MAIN CONTENT -->
    <main class="min-h-screen flex items-center justify-center px-4 pt-20 pb-16">
        <div class="w-full max-w-xl">

            <?= Flash::render() ?>

            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl p-8">

                <!-- Header Logo & Greeting -->
                <div class="text-center mb-7">
                    <a href="login">
                        <div class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-gradient-to-tr from-sky-600 to-sky-400 text-white shadow-lg shadow-sky-500/20 mb-3">
                            <i data-lucide="trending-up" class="w-6 h-6"></i>
                        </div>
                    </a>
                    <h1 class="text-xl font-bold text-zinc-900 dark:text-zinc-100">Create an account</h1>
                    <p class="text-sm text-zinc-500 mt-1">Fill in your information to get started</p>
                </div>

                <!-- Form -->
                <form method="POST" action="register" class="space-y-4">
                    <?= Csrf::field() ?>

                    <!-- First & Last Name -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-zinc-500 dark:text-zinc-400 mb-1.5 uppercase tracking-wider">First Name</label>
                            <input type="text" name="first_name" placeholder="John" required
                                class="w-full bg-zinc-100 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 rounded-lg px-3 py-2.5 text-sm text-zinc-900 dark:text-zinc-100 focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500/50 transition-colors">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-zinc-500 dark:text-zinc-400 mb-1.5 uppercase tracking-wider">Last Name</label>
                            <input type="text" name="last_name" placeholder="Doe" required
                                class="w-full bg-zinc-100 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 rounded-lg px-3 py-2.5 text-sm text-zinc-900 dark:text-zinc-100 focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500/50 transition-colors">
                        </div>
                    </div>

                    <!-- Username & Email -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-zinc-500 dark:text-zinc-400 mb-1.5 uppercase tracking-wider">Username</label>
                            <input type="text" id="username" name="username" placeholder="john_doe" required
                                class="w-full bg-zinc-100 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 rounded-lg px-3 py-2.5 text-sm text-zinc-900 dark:text-zinc-100 focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500/50 transition-colors">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-zinc-500 dark:text-zinc-400 mb-1.5 uppercase tracking-wider">Email Address</label>
                            <input type="email" name="email" placeholder="you@example.com" required
                                class="w-full bg-zinc-100 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 rounded-lg px-3 py-2.5 text-sm text-zinc-900 dark:text-zinc-100 focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500/50 transition-colors">
                        </div>
                    </div>

                    <!-- Phone & Referral Code -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-zinc-500 dark:text-zinc-400 mb-1.5 uppercase tracking-wider">Phone Number</label>
                            <input type="tel" name="phone" placeholder="+1 (555) 000-0000"
                                class="w-full bg-zinc-100 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 rounded-lg px-3 py-2.5 text-sm text-zinc-900 dark:text-zinc-100 focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500/50 transition-colors">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-zinc-500 dark:text-zinc-400 mb-1.5 uppercase tracking-wider">Referral ID <span class="normal-case text-zinc-600">(optional)</span></label>
                            <input type="text" name="referral_code" value="<?= htmlspecialchars($ref ?? '') ?>" placeholder="Referral ID"
                                class="w-full bg-zinc-100 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 rounded-lg px-3 py-2.5 text-sm text-zinc-900 dark:text-zinc-100 focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500/50 transition-colors">
                        </div>
                    </div>

                    <!-- Password & Confirm -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div x-data="{ show: false }">
                            <label class="block text-xs font-medium text-zinc-500 dark:text-zinc-400 mb-1.5 uppercase tracking-wider">Password</label>
                            <div class="relative">
                                <input :type="show ? 'text' : 'password'" name="password" placeholder="Create a password" required minlength="6"
                                    class="w-full bg-zinc-100 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 rounded-lg px-3 py-2.5 text-sm text-zinc-900 dark:text-zinc-100 focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500/50 transition-colors pr-10">
                                <button type="button" @click="show = !show" class="absolute inset-y-0 right-3 flex items-center text-zinc-400 hover:text-zinc-300 transition-colors">
                                    <i :data-lucide="show ? 'eye-off' : 'eye'" class="w-4 h-4" x-effect="$nextTick(() => lucide.createIcons())"></i>
                                </button>
                            </div>
                        </div>

                        <div x-data="{ show: false }">
                            <label class="block text-xs font-medium text-zinc-500 dark:text-zinc-400 mb-1.5 uppercase tracking-wider">Confirm Password</label>
                            <div class="relative">
                                <input :type="show ? 'text' : 'password'" name="password_confirmation" placeholder="Repeat your password" required minlength="6"
                                    class="w-full bg-zinc-100 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 rounded-lg px-3 py-2.5 text-sm text-zinc-900 dark:text-zinc-100 focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500/50 transition-colors pr-10">
                                <button type="button" @click="show = !show" class="absolute inset-y-0 right-3 flex items-center text-zinc-400 hover:text-zinc-300 transition-colors">
                                    <i :data-lucide="show ? 'eye-off' : 'eye'" class="w-4 h-4" x-effect="$nextTick(() => lucide.createIcons())"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <button type="submit"
                        class="w-full bg-sky-600 hover:bg-sky-500 text-white font-medium py-2.5 rounded-lg transition-colors text-sm flex items-center justify-center gap-2 mt-4 cursor-pointer">
                        <i data-lucide="user-plus" class="w-4 h-4"></i>
                        Create Account
                    </button>
                </form>

                <p class="text-center text-sm text-zinc-500 mt-6">
                    Already have an account?
                    <a href="login" class="text-sky-500 hover:text-sky-400 font-medium transition-colors">
                        Sign in
                    </a>
                </p>

            </div>
        </div>
    </main>

    <!-- FOOTER -->
    <footer class="fixed bottom-0 inset-x-0 py-3 text-center border-t border-zinc-200 dark:border-zinc-800 bg-white/70 dark:bg-zinc-900/70 backdrop-blur-sm z-10">
        <div class="flex items-center justify-center gap-4 flex-wrap">
            <span class="text-xs text-zinc-500">&copy; <?= date('Y') ?> Task Investment. All rights reserved.</span>
        </div>
    </footer>

    <!-- SCRIPTS -->
    <script>
        document.addEventListener('DOMContentLoaded', function () { lucide.createIcons(); });

        function authTheme() {
            return {
                isDark: true,
                init() {
                    var saved = localStorage.getItem('ud_theme');
                    this.isDark = saved !== null ? saved === 'dark' : true;
                    this.applyTheme();
                },
                toggleTheme() {
                    this.isDark = !this.isDark;
                    localStorage.setItem('ud_theme', this.isDark ? 'dark' : 'light');
                    this.applyTheme();
                    lucide.createIcons();
                },
                applyTheme() {
                    if (this.isDark) {
                        document.documentElement.classList.add('dark');
                    } else {
                        document.documentElement.classList.remove('dark');
                    }
                }
            }
        }
    </script>
</body>
</html>
