<?php

declare(strict_types=1);

/**
 * Env
 *
 * Tiny, dependency-free .env loader.
 *
 * - Loads key=value pairs from the .env file (if present).
 * - Never overrides variables already set in the real environment.
 *   This matters on Render, where DB_* values are injected as real
 *   environment variables and should win over any .env file.
 */

final class Env
{
    private static bool $loaded = false;

    public static function load(string $file = null): void
    {
        if (self::$loaded) {
            return;
        }
        self::$loaded = true;

        $file ??= dirname(__DIR__) . '/.env';

        if (!is_file($file) || !is_readable($file)) {
            return;
        }

        $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {
            $line = trim($line);

            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }

            if (!str_contains($line, '=')) {
                continue;
            }

            [$key, $value] = array_map('trim', explode('=', $line, 2));

            // Strip surrounding quotes.
            $value = trim($value);
            if (strlen($value) >= 2 && in_array($value[0], ['"', "'"], true)) {
                $quote = $value[0];
                $value = trim($value, $quote);
            }

            if ($key === '') {
                continue;
            }

            if (getenv($key) === false && !array_key_exists($key, $_ENV)) {
                putenv(sprintf('%s=%s', $key, $value));
                $_ENV[$key] = $value;
            }
        }
    }

    public static function get(string $key, ?string $default = null): ?string
    {
        $value = getenv($key);

        if ($value === false) {
            $value = $_ENV[$key] ?? $default;
        }

        return is_string($value) ? $value : $default;
    }
}