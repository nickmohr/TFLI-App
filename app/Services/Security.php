<?php

declare(strict_types=1);

namespace App\Services;

class Security
{
    private static ?string $cspNonce = null;

    public static function sendSecurityHeaders(): void
    {
        $nonce = self::cspNonce();
        header_remove('X-Powered-By');
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: DENY');
        header('Referrer-Policy: strict-origin-when-cross-origin');
        header('Cross-Origin-Opener-Policy: same-origin');
        header('Cross-Origin-Resource-Policy: same-origin');
        header("Content-Security-Policy: default-src 'self'; script-src 'self' 'nonce-{$nonce}' https://cdn.tailwindcss.com; script-src-attr 'none'; style-src 'self' 'unsafe-inline'; style-src-attr 'none'; font-src 'self'; img-src 'self' data: https://www.tfli.co.uk; connect-src 'self'; frame-src 'none'; worker-src 'self'; frame-ancestors 'none'; base-uri 'self'; form-action 'self'; object-src 'none'; upgrade-insecure-requests;");
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
