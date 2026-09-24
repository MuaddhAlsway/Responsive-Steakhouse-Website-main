<?php

declare(strict_types=1);

/**
 * Database
 *
 * PDO singleton used by all models. Credentials always come from
 * environment variables (see .env.example).
 *
 * - PDO::ERRMODE_EXCEPTION  -> SQL errors become exceptions.
 * - PDO::ATTR_EMULATE_PREPARES = false -> real prepared statements.
 * - charset utf8mb4         -> full Unicode support.
 */

final class Database
{
    private static ?PDO $connection = null;

    public static function connect(): PDO
    {
        if (self::$connection instanceof PDO) {
            return self::$connection;
        }

        Env::load();

        $host = Env::get('DB_HOST', '127.0.0.1');
        $port = Env::get('DB_PORT', '3306');
        $name = Env::get('DB_NAME', 'steakhouse');
        $user = Env::get('DB_USER', 'root');
        $pass = Env::get('DB_PASSWORD', '');

        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
            $host,
            $port,
            $name
        );

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            self::$connection = new PDO($dsn, $user, $pass, $options);
        } catch (PDOException $e) {
            // Never expose connection details to the client.
            error_log('[Database] connection failed: ' . $e->getMessage());
            throw new RuntimeException('Database connection failed.');
        }

        return self::$connection;
    }
}