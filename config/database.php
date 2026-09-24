<?php

declare(strict_types=1);

/**
 * Database (bridge)
 *
 * Convenience file that exposes the PDO connection to the rest of the app.
 * Usage:
 *   $db = db();            // PDO instance
 *   db()->prepare(...);    // works as well
 */

require_once __DIR__ . '/bootstrap.php';

function db(): PDO
{
    return Database::connect();
}