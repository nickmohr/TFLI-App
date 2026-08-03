<?php

declare(strict_types=1);

namespace App\Controllers;

class UrlController
{
    public function index(): void
    {
        echo 'Hello, World!';
    }

    public function redirect(string $path): void
    {
        echo "Redirecting to: $path";
    }
}