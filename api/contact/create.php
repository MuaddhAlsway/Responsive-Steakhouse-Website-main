<?php

declare(strict_types=1);

/**
 * POST /api/contact/create.php
 *
 * Store a contact message.
 *
 * Responses:
 *   201 Created              message stored
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

RateLimiter::check('contact_create');

$body = json_decode((string) file_get_contents('php://input'), true);

if (!is_array($body)) {
    Response::error('Invalid JSON body.', 400);
}

$errors = Contact::validate($body);

if ($errors !== []) {
    Response::error('Please fix the highlighted fields.', 422, $errors);
}

try {
    $model = new Contact(Database::connect());
    $model->create($body);

    Response::success('Message sent successfully. We will get back to you soon.', null, 201);
} catch (Throwable $e) {
    error_log('[Contact.create] ' . $e->getMessage());
    Response::error('Something went wrong while sending your message. Please try again later.', 500);
}