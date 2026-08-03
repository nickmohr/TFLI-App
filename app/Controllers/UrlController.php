<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\View;
use App\Services\Router;
use App\Models\Url;

class UrlController
{
    public function create(): void
    {

        $post = filter_input_array(INPUT_POST, [
            'url' => FILTER_SANITIZE_URL,
            'expires_at' => FILTER_UNSAFE_RAW,
        ]);

        $url = isset($post['url']) ? trim((string) $post['url']) : '';
        $expiresRaw = isset($post['expires_at']) ? trim((string) $post['expires_at']) : '';

        $expiresAt = null;
        $errors = [];

        if (
            filter_var($url, FILTER_VALIDATE_URL) === false
            || !in_array(parse_url($url, PHP_URL_SCHEME), ['http', 'https'], true)
        ) {
            $errors[] = 'Please provide a valid URL starting with http:// or https://.';
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
            View::renderJson($errors, 400);
            return;
        }

        if ($existing = Url::findByUrlExpiry($url, $expiresAt)) {
            $shortUrl = Router::baseUrl() . '/url/' . $existing['code'];
        } else {
            try {
                $code = (new Url())->createCode($url, $expiresAt);
                $shortUrl = Router::baseUrl() . '/url/' . $code;
            } catch (\RuntimeException $e) {
                //catch the exception and respond with an error message instead of throwing it
                View::renderJson(['error' => 'Failed to generate a unique short code. Please try again.'], 500);
                return;
            }
        }

        View::renderJson(['short_url' => $shortUrl], 201);
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
        exit;
    }
}
