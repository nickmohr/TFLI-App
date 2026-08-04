<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Url;
use App\Services\Router;
use App\Services\Security;
use App\Services\View;

class UrlController
{
    private const URL_MAX_LENGTH = 2048;
    private const EXPIRES_AT_MAX_LENGTH = 19;

    public function create(): void
    {
        $csrfToken = null;
        if (isset($_SERVER['HTTP_X_CSRF_TOKEN'])) {
            $csrfToken = trim((string) $_SERVER['HTTP_X_CSRF_TOKEN']);
        }

        if (!Security::verifyCsrfToken($csrfToken)) {
            View::renderJson(['success' => false, 'error' => 'Invalid CSRF token. Please refresh and try again.'], 419);
            return;
        }

        $post = filter_input_array(INPUT_POST, [
            'url' => FILTER_UNSAFE_RAW,
            'expires_at' => FILTER_UNSAFE_RAW,
        ]);

        $url = isset($post['url']) ? trim((string) $post['url']) : '';
        $expiresRaw = isset($post['expires_at']) ? trim((string) $post['expires_at']) : '';

        $expiresAt = null;
        $errors = [];

        if (strlen($url) > self::URL_MAX_LENGTH) {
            $errors[] = 'URL must be 2048 characters or fewer.';
        } elseif (
            filter_var($url, FILTER_VALIDATE_URL) === false
            || !in_array(parse_url($url, PHP_URL_SCHEME), ['http', 'https'], true)
        ) {
            $errors[] = 'Please provide a valid URL starting with http:// or https://.';
        }

        if (strlen($expiresRaw) > self::EXPIRES_AT_MAX_LENGTH) {
            $errors[] = 'Expiry date value is too long.';
        } elseif ($expiresRaw !== '') {
            $expiry = \DateTimeImmutable::createFromFormat('!Y-m-d\TH:i', $expiresRaw)
                ?: \DateTimeImmutable::createFromFormat('!Y-m-d\TH:i:s', $expiresRaw);

            if ($expiry === false) {
                $errors[] = 'Invalid expiry date.';
            } elseif ($expiry <= new \DateTimeImmutable()) {
                $errors[] = 'Expiry must be in the future.';
            } else {
                $expiresAt = $expiry->format('Y-m-d H:i:s');
            }
        }

        if (!empty($errors)) {
            View::renderJson(['success' => false, 'errors' => $errors], 400);
            return;
        }

        if ($existing = Url::findByUrlExpiry($url, $expiresAt)) {
            $shortUrl = Router::baseUrl() . '/url/' . $existing['code'];
        } else {
            try {
                $code = Url::createCode($url, $expiresAt);
                $shortUrl = Router::baseUrl() . '/url/' . $code;
            } catch (\RuntimeException $e) {
                //catch the exception and respond with an error message instead of throwing it
                View::renderJson(['success' => false, 'error' => 'Failed to generate a unique short code. Please try again.'], 500);
                return;
            }
        }

        View::renderJson(['success' => true, 'short_url' => $shortUrl], 201);
        return;
    }

    public function redirect(string $code): void
    {
        $row = Url::findByCode($code);

        $expired = $row !== null
            && $row['expires_at'] !== null
            && strtotime((string) $row['expires_at']) <= time();

        if ($row === null || $expired) {
            View::render('404', 404);
            return;
        }

        header('Location: ' . $row['long_url'], true, 302);
        header('Cache-Control: no-cache, no-store');
        exit;
    }
}
