<?php

declare(strict_types=1);

/**
 * GET /api/reservations/list.php
 *
 * List all reservations (site-owner endpoint).
 *
 * If ADMIN_API_TOKEN is configured, requests must send the token as:
 *   - Authorization: Bearer <token>
 *   - X-API-Key: <token>
 *   - or ?token=<token>
 */

require_once __DIR__ . '/../../config/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    Response::error('Method not allowed.', 405, ['allowed' => ['GET']]);
}

$expectedToken = Env::get('ADMIN_API_TOKEN', '');

if ($expectedToken !== '') {
    $provided = (string) ($_SERVER['HTTP_AUTHORIZATION'] ?? '');
    if (preg_match('/^Bearer\s+(.+)$/i', $provided, $m)) {
        $provided = trim($m[1]);
    } else {
        $provided = (string) ($_SERVER['HTTP_X_API_KEY'] ?? ($_GET['token'] ?? ''));
    }

    if (!hash_equals($expectedToken, $provided)) {
        Response::error('Unauthorized.', 401);
    }
}

try {
    $model = new Reservation(Database::connect());
    Response::success('Reservations retrieved.', $model->list(), 200);
} catch (Throwable $e) {
    error_log('[Reservation.list] ' . $e->getMessage());
    Response::error('Something went wrong while loading reservations.', 500);
}