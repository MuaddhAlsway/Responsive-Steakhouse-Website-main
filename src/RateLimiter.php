<?php

declare(strict_types=1);

/**
 * RateLimiter
 *
 * Lightweight, dependency-free sliding-window rate limiter.
 * Requests are bucketed per endpoint + client IP.
 *
 * Limits can be tuned with environment variables:
 *   RATE_LIMIT_MAX              (default 10)
 *   RATE_LIMIT_WINDOW_SECONDS   (default 60)
 *
 * This protects public write endpoints (reservations / contact)
 * from simple abuse. It is not a substitute for a dedicated
 * WAF or edge rate-limiting on production.
 */

final class RateLimiter
{
    public static function check(string $bucket = 'global'): void
    {
        $max = (int) Env::get('RATE_LIMIT_MAX', '10');
        $window = (int) Env::get('RATE_LIMIT_WINDOW_SECONDS', '60');

        if ($max <= 0 || $window <= 0) {
            return; // rate limiting disabled via config
        }

        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $key = hash('sha256', $bucket . '|' . $ip);

        $dir = self::storageDir();
        $file = $dir . '/' . $key . '.json';
        $now = time();

        $entries = [];
        if (is_file($file)) {
            $decoded = json_decode((string) file_get_contents($file), true);
            if (is_array($decoded)) {
                $entries = $decoded;
            }
        }

        // Keep only entries inside the sliding window.
        $entries = array_values(array_filter(
            $entries,
            static fn ($t): bool => $now - (int) $t < $window
        ));

        if (count($entries) >= $max) {
            Response::error('Too many requests. Please try again in a minute.', 429);
        }

        $entries[] = $now;

        file_put_contents($file, json_encode($entries), LOCK_EX);
    }

    private static function storageDir(): string
    {
        $dir = rtrim(sys_get_temp_dir(), '/\\') . DIRECTORY_SEPARATOR . 'steakhouse_rate_limit';

        if (!is_dir($dir)) {
            mkdir($dir, 0700, true);
        }

        return $dir;
    }
}