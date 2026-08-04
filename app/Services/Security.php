<?php

declare(strict_types=1);

namespace App\Services;

class Security
{
    private static ?string $cspNonce = null;

    public static function startSession(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        session_set_cookie_params([
            'lifetime' => 0,
            'path' => '/',
            'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
            'httponly' => true,
            'samesite' => 'Strict',
        ]);

        session_start();
    }

    public static function csrfToken(): string
    {
        self::startSession();

        if (!isset($_SESSION['csrf_token']) || !is_string($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = base64_encode(random_bytes(32));
        }

        return $_SESSION['csrf_token'];
    }

    public static function verifyCsrfToken(?string $token): bool
    {
        self::startSession();

        if (!isset($_SESSION['csrf_token']) || !is_string($_SESSION['csrf_token'])) {
            return false;
        }

        if ($token === null || $token === '') {
            return false;
        }

        return hash_equals($_SESSION['csrf_token'], $token);
    }

    public static function sendSecurityHeaders(): void
    {
        $nonce = self::cspNonce();
        header_remove('X-Powered-By');
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: DENY');
        header('Referrer-Policy: strict-origin-when-cross-origin');
        header('Cross-Origin-Opener-Policy: same-origin');
        header('Cross-Origin-Resource-Policy: same-origin');
        header("Content-Security-Policy: default-src 'self'; script-src 'self' 'nonce-{$nonce}' 'sha256-ZswfTY7H35rbv8WC7NXBoiC7WNu86vSzCDChNWwZZDM=' https://cdn.tailwindcss.com; script-src-attr 'none'; style-src 'self' 'unsafe-inline'; style-src-attr 'none'; font-src 'self'; img-src 'self' data: https://www.tfli.co.uk; connect-src 'self'; frame-src 'none'; worker-src 'self'; frame-ancestors 'none'; base-uri 'self'; form-action 'self'; object-src 'none';");
        header('Permissions-Policy: accelerometer=(), autoplay=(), camera=(), display-capture=(), encrypted-media=(), fullscreen=(self), geolocation=(), gyroscope=(), magnetometer=(), microphone=(), midi=(), payment=(), picture-in-picture=(), publickey-credentials-get=(), screen-wake-lock=(), sync-xhr=(), usb=(), xr-spatial-tracking=(), interest-cohort=()');
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains; preload');
    }

    /**
     * Generate or return the per-request CSP nonce.
     */
    public static function cspNonce(): string
    {
        if (self::$cspNonce === null) {
            self::$cspNonce = base64_encode(random_bytes(16));
        }
        return self::$cspNonce;
    }
}
