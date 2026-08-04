<?php

declare(strict_types=1);

date_default_timezone_set('UTC');

define('APP_ROOT', dirname(__DIR__));
define('DB_DSN', 'sqlite:' . APP_ROOT . '/app/Data/database.sqlite');
define('APP_VERSION', '1.0.0');
define('APP_NAME', 'TFLI-App');
//SET APP_ENV to 'development' for development environment, 'production' for production environment (turns off error reporting and uses APP_URL for base URL)
define('APP_ENV', 'development');
//APP_URL is only used when APP_ENV is set to 'production'. In development, the app will use the current host and port from the request.
define('APP_URL', 'https://yourdomain.com');

