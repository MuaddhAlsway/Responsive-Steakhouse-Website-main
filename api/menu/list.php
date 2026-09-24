<?php

declare(strict_types=1);

/**
 * GET /api/menu/list.php
 *
 * Return the list of available menu items as JSON.
 *
 * Optional query parameter: ?category=starter|main|salad|dessert
 *
 * Responses:
 *   200 OK
 *   405 Method Not Allowed   non-GET request
 *   500 Internal Server Error generic failure (details logged)
 */

require_once __DIR__ . '/../../config/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    Response::error('Method not allowed.', 405, ['allowed' => ['GET']]);
}

$category = isset($_GET['category']) ? trim((string) $_GET['category']) : null;

$allowedCategories = ['starter', 'main', 'salad', 'dessert'];

if ($category !== null && $category !== '' && !in_array($category, $allowedCategories, true)) {
    Response::error('Invalid category.', 422, ['category' => 'Allowed values: ' . implode(', ', $allowedCategories)]);
}

try {
    $model = new Menu(Database::connect());
    Response::success('Menu items retrieved.', $model->list($category), 200);
} catch (Throwable $e) {
    error_log('[Menu.list] ' . $e->getMessage());
    Response::error('Something went wrong while loading the menu.', 500);
}