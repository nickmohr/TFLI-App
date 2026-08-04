<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\Security;
use App\Services\View;

class IndexController
{
    public function index(): void
    {
        $nonce = Security::cspNonce();
        View::render('index', 200, ['nonce' => $nonce]);
    }
}
