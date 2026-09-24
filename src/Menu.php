<?php

declare(strict_types=1);

/**
 * Menu
 *
 * Read model for the menu_items table. Only available items are exposed.
 */

final class Menu
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * List available menu items, optionally filtered by category.
     *
     * @return array<int, array<string, mixed>>
     */
    public function list(?string $category = null): array
    {
        if ($category !== null) {
            $stmt = $this->db->prepare(
                'SELECT id, name, description, price, category, image
                 FROM menu_items
                 WHERE is_available = 1 AND category = :category
                 ORDER BY category, id'
            );
            $stmt->execute([':category' => $category]);
        } else {
            $stmt = $this->db->query(
                'SELECT id, name, description, price, category, image
                 FROM menu_items
                 WHERE is_available = 1
                 ORDER BY category, id'
            );
        }

        $items = $stmt->fetchAll();

        // DECIMAL values arrive as strings in PDO; cast to float for JSON.
        foreach ($items as &$item) {
            $item['price'] = round((float) $item['price'], 2);
        }
        unset($item);

        return $items;
    }
}