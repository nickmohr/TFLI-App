<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\View;

class IndexController
{
    public function index(): void
    {
        View::render('index');
    }
}
