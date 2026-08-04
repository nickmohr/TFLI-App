<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\View;
use App\Services\Security;

class IndexController
{
    public function index(): void
    {
        $nonce = Security::cspNonce();
        View::render('index', 200, ['nonce' => $nonce]);
    }
}
