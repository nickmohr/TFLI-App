<?php

declare(strict_types=1);


require_once __DIR__ . '/config.php';
require_once __DIR__ . '/../vendor/autoload.php';

use App\Controllers\IndexController;
use App\Controllers\UrlController;
use App\Services\Database;
use App\Services\Router;
use App\Services\Security;

Security::startSession();
Security::sendSecurityHeaders();

//optional: enable error reporting for development environment
if (APP_ENV === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');

} else {
    error_reporting(E_ALL);
    ini_set('display_errors', '0');
    ini_set('display_startup_errors', '0');

}
ini_set('log_errors', '1');
ini_set('error_log', APP_ROOT . '/app/Data/error.log');

Database::init();

$router = new Router();

$router->add('/', 'GET', IndexController::class, 'index');
$router->add('/', 'POST', UrlController::class, 'create');
$router->add('/url/{code}', 'GET', UrlController::class, 'redirect');

$router->dispatch();
