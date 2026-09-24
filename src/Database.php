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
 * - TLS                    -> enabled in production (Aiven MySQL requires it).
 *                             - DB_SSL=true forces TLS on; DB_SSL=false forces it off.
 *                             - When APP_ENV=production and DB_SSL is unset,
 *                               TLS is enabled by default.
 *                             - DB_SSL_CA points at the CA certificate file
 *                               (default /etc/ssl/certs/aiven-ca.pem in the container).
 *                             - DB_SSL_VERIFY_SERVER_CERT defaults to true;
 *                               never disable verification for Aiven.
 *                             - Fails closed if TLS is on but the CA file is missing.
 *                             Local XAMPP: no TLS vars/APP_ENV=local -> plain
 *                             local connection as before.
 */

final class Database
{
    /** Default CA file path inside the Render container. */
    private const DEFAULT_CA_PATH = '/etc/ssl/certs/aiven-ca.pem';

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

        if (self::useTls()) {
            $caPath = Env::get('DB_SSL_CA', self::DEFAULT_CA_PATH);
            $verify = self::truthy(Env::get('DB_SSL_VERIFY_SERVER_CERT', 'true'));

            // Fail closed: never connect over TLS without a verifiable
            // certificate chain. The details go to the log only.
            if ($verify && !is_file($caPath)) {
                error_log('[Database] TLS enabled but CA certificate not found at ' . $caPath);
                error_log('[Database] Provide the Aiven CA via the AIVEN_CA_CERT env var or a mounted secret file.');
                throw new RuntimeException('Database connection failed.');
            }

            $options[PDO::MYSQL_ATTR_SSL_CA] = $caPath;
            $options[PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = $verify;
        }

        try {
            self::$connection = new PDO($dsn, $user, $pass, $options);
        } catch (PDOException $e) {
            // Never expose connection details to the client.
            error_log('[Database] connection failed: ' . $e->getMessage());
            throw new RuntimeException('Database connection failed.');
        }

        return self::$connection;
    }

    /**
     * Whether the PDO connection must use TLS.
     * - DB_SSL explicitly set (1/true/yes/on) -> that value wins.
     * - otherwise: production environments default to TLS (Aiven needs it).
     */
    private static function useTls(): bool
    {
        $explicit = Env::get('DB_SSL');

        if ($explicit !== null && $explicit !== '') {
            return self::truthy($explicit);
        }

        return Env::get('APP_ENV', 'local') === 'production';
    }

    private static function truthy(string $value): bool
    {
        return in_array(strtolower(trim($value)), ['1', 'true', 'yes', 'on'], true);
    }
}