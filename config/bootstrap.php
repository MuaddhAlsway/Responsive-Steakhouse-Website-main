<?php

declare(strict_types=1);

/**
 * Bootstrap
 *
 * Central file that loads environment configuration and all source classes.
 * Every API endpoint and script should require this file first.
 */

require_once __DIR__ . '/env.php';
require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/Response.php';
require_once __DIR__ . '/../src/RateLimiter.php';
require_once __DIR__ . '/../src/Reservation.php';
require_once __DIR__ . '/../src/Contact.php';
require_once __DIR__ . '/../src/Menu.php';

Env::load();

// Do not leak error details to the browser in production.
if (Env::get('APP_ENV', 'local') === 'production') {
    ini_set('display_errors', '0');
    ini_set('display_startup_errors', '0');
    error_reporting(E_ALL);
    ini_set('log_errors', '1');
}