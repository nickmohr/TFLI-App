<?php

declare(strict_types=1);


require_once __DIR__ . '/config.php';
require_once __DIR__ . '/../vendor/autoload.php';

use App\Controllers\IndexController;
use App\Controllers\UrlController;
use App\Services\Database;
use App\Services\Router;
use App\Services\Security;

Security::sendSecurityHeaders();

//optional: enable error reporting for development environment
if (APP_ENV === 'development') {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    ini_set('display_startup_errors', '0');
    error_reporting(0);
}

Database::init();

$router = new Router();

$router->add('/', 'GET', IndexController::class, 'index');
$router->add('/', 'POST', UrlController::class, 'create');
$router->add('/url/{code}', 'GET', UrlController::class, 'redirect');

$router->dispatch();
