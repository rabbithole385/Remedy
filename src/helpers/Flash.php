<?php
// ============================================================
// Remedy Investment — Flash Messages Helper
// ============================================================

class Flash {
    public static function set(string $type, string $message): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['flash_messages'][] = [
            'type'    => $type, // success, error, warning, info
            'message' => $message,
        ];
    }

    public static function success(string $message): void {
        self::set('success', $message);
    }

    public static function error(string $message): void {
        self::set('error', $message);
    }

    public static function warning(string $message): void {
        self::set('warning', $message);
    }

    public static function info(string $message): void {
        self::set('info', $message);
    }

    public static function get(): array {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $messages = $_SESSION['flash_messages'] ?? [];
        unset($_SESSION['flash_messages']);
        return $messages;
    }

    public static function render(): string {
        $messages = self::get();
        if (empty($messages)) return '';

        $html = '<div class="space-y-3 mb-6">';
        foreach ($messages as $msg) {
            $type = $msg['type'];
            $text = htmlspecialchars($msg['message'], ENT_QUOTES, 'UTF-8');

            $classes = match($type) {
                'success' => 'bg-emerald-500/10 border-emerald-500/30 text-emerald-400',
                'error'   => 'bg-rose-500/10 border-rose-500/30 text-rose-400',
                'warning' => 'bg-amber-500/10 border-amber-500/30 text-amber-400',
                default   => 'bg-sky-500/10 border-sky-500/30 text-sky-400',
            };

            $icon = match($type) {
                'success' => '<i data-lucide="check-circle" class="w-5 h-5 flex-shrink-0"></i>',
                'error'   => '<i data-lucide="alert-circle" class="w-5 h-5 flex-shrink-0"></i>',
                'warning' => '<i data-lucide="alert-triangle" class="w-5 h-5 flex-shrink-0"></i>',
                default   => '<i data-lucide="info" class="w-5 h-5 flex-shrink-0"></i>',
            };

            $html .= "
            <div class=\"flex items-center justify-between p-4 rounded-xl border backdrop-blur-md {$classes} shadow-lg\" x-data=\"{ show: true }\" x-show=\"show\" x-transition>
                <div class=\"flex items-center gap-3\">
                    {$icon}
                    <span class=\"text-sm font-medium\">{$text}</span>
                </div>
                <button type=\"button\" @click=\"show = false\" class=\"p-1 hover:opacity-75 transition-opacity\">
                    <i data-lucide=\"x\" class=\"w-4 h-4\"></i>
                </button>
            </div>";
        }
        $html .= '</div>';
        return $html;
    }
}
