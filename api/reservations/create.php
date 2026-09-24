<?php

declare(strict_types=1);

/**
 * POST /api/reservations/create.php
 *
 * Create a new reservation.
 *
 * Responses:
 *   201 Created              reservation stored
 *   400 Bad Request          malformed JSON body
 *   405 Method Not Allowed   non-POST request
 *   422 Unprocessable Entity validation failed
 *   429 Too Many Requests    rate limit hit
 *   500 Internal Server Error generic failure (details logged)
 */

require_once __DIR__ . '/../../config/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    Response::error('Method not allowed.', 405, ['allowed' => ['POST']]);
}

RateLimiter::check('reservation_create');

$body = json_decode((string) file_get_contents('php://input'), true);

if (!is_array($body)) {
    Response::error('Invalid JSON body.', 400);
}

$errors = Reservation::validate($body);

if ($errors !== []) {
    Response::error('Please fix the highlighted fields.', 422, $errors);
}

try {
    $model = new Reservation(Database::connect());
    $model->create($body);

    Response::success('Reservation submitted successfully. We will call you shortly to confirm.', null, 201);
} catch (Throwable $e) {
    error_log('[Reservation.create] ' . $e->getMessage());
    Response::error('Something went wrong while saving your reservation. Please try again later.', 500);
}