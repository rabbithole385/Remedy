<?php
// ============================================================
// Remedy Investment — Crypto & Financial Helper
// ============================================================

require_once __DIR__ . '/../config/db.php';

class Crypto {
    /**
     * Get configured wallet address for a given coin type
     */
    public static function getWalletAddress(string $type): string {
        $key = match(strtoupper($type)) {
            'USDT-TRC20', 'TRC20' => 'usdt_trc20_wallet',
            'USDT-ERC20', 'ERC20' => 'usdt_erc20_wallet',
            'BTC'                 => 'btc_wallet',
            default               => 'usdt_trc20_wallet',
        };

        $setting = DB::fetch("SELECT value FROM settings WHERE `key` = ?", [$key]);
        if ($setting && !empty($setting['value'])) {
            return $setting['value'];
        }

        return match(strtoupper($type)) {
            'USDT-TRC20', 'TRC20' => CRYPTO_USDT_TRC20 ?: 'TRemedyUSDTTRC20OfficialWallet2026',
            'USDT-ERC20', 'ERC20' => CRYPTO_USDT_ERC20 ?: '0xRemedyUSDTERC20OfficialWallet2026',
            'BTC'                 => CRYPTO_BTC ?: '1RemedyBTCOfficialWalletAddress2026',
            default               => 'TRemedyUSDTTRC20OfficialWallet2026',
        };
    }

    /**
     * Truncate address for safe display (e.g. 0x1234...abcd)
     */
    public static function truncate(string $address, int $start = 6, int $end = 4): string {
        if (strlen($address) <= $start + $end) return $address;
        return substr($address, 0, $start) . '...' . substr($address, -$end);
    }

    /**
     * Generate unique transaction reference (e.g. REM-TX-20261010-ABCD12)
     */
    public static function generateReference(string $prefix = 'TX'): string {
        return 'REM-' . strtoupper($prefix) . '-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(3)));
    }

    /**
     * Format currency display
     */
    public static function formatMoney(float|int|string $amount): string {
        return '$' . number_format((float)$amount, 2);
    }
}
