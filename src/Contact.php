<?php

declare(strict_types=1);

/**
 * Contact
 *
 * Model for the contact_messages table. Independent server-side
 * validation + persistence through PDO prepared statements.
 */

final class Contact
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Validate raw input. Returns a map of field => error message.
     * Empty array means "valid".
     *
     * @param array<string, mixed> $input
     * @return array<string, string>
     */
    public static function validate(array $input): array
    {
        $errors = [];

        $name = isset($input['name']) ? trim((string) $input['name']) : '';
        if ($name === '') {
            $errors['name'] = 'Your name is required.';
        } elseif (mb_strlen($name) > 100) {
            $errors['name'] = 'Name must be 100 characters or less.';
        }

        $email = isset($input['email']) ? trim((string) $input['email']) : '';
        if ($email === '') {
            $errors['email'] = 'An email address is required.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'The email address is not valid.';
        } elseif (mb_strlen($email) > 100) {
            $errors['email'] = 'Email must be 100 characters or less.';
        }

        $phone = isset($input['phone']) ? trim((string) $input['phone']) : '';
        if ($phone !== '' && mb_strlen($phone) > 30) {
            $errors['phone'] = 'Phone number must be 30 characters or less.';
        }

        $subject = isset($input['subject']) ? trim((string) $input['subject']) : '';
        if ($subject === '') {
            $errors['subject'] = 'A subject is required.';
        } elseif (mb_strlen($subject) > 150) {
            $errors['subject'] = 'Subject must be 150 characters or less.';
        }

        $message = isset($input['message']) ? trim((string) $input['message']) : '';
        if ($message === '') {
            $errors['message'] = 'A message is required.';
        } elseif (mb_strlen($message) > 2000) {
            $errors['message'] = 'Message must be 2000 characters or less.';
        }

        return $errors;
    }

    /**
     * Insert a new contact message. Returns the new row id.
     *
     * @param array<string, mixed> $input validated input
     */
    public function create(array $input): int
    {
        $sql = 'INSERT INTO contact_messages (name, email, phone, subject, message)
                VALUES (:name, :email, :phone, :subject, :message)';

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':name'    => trim((string) $input['name']),
            ':email'   => trim((string) $input['email']),
            ':phone'   => isset($input['phone']) ? trim((string) $input['phone']) : '',
            ':subject' => trim((string) $input['subject']),
            ':message' => trim((string) $input['message']),
        ]);

        return (int) $this->db->lastInsertId();
    }

    /**
     * List all contact messages (intended for the site owner).
     *
     * @return array<int, array<string, mixed>>
     */
    public function list(): array
    {
        $stmt = $this->db->query(
            'SELECT id, name, email, phone, subject, message, created_at
             FROM contact_messages
             ORDER BY created_at DESC'
        );

        return $stmt->fetchAll();
    }
}