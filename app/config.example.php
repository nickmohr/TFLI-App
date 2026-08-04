<?php

declare(strict_types=1);

define('APP_ROOT', dirname(__DIR__));
define('DB_DSN', 'sqlite:' . APP_ROOT . '/app/Database/database.sqlite');
define('APP_VERSION', '1.0.0');
define('APP_NAME', 'TFLI-App');
//SET APP_ENV to 'development' for development environment, 'production' for production environment (turns off error reporting)
define('APP_ENV', 'development');