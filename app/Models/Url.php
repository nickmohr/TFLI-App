<?php

declare(strict_types=1);

namespace App\Models;

class Url
{
    public function createCode(string $url, ?\DateTimeImmutable $expires_at): void
    {
        echo "Storing URL: $url";
    }




}