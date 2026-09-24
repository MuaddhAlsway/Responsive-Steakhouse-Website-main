<?php

declare(strict_types=1);

/**
 * Reservation
 *
 * Model for the reservations table. Handles independent server-side
 * validation and all persistence through PDO prepared statements.
 */

final class Reservation
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_COMPLETED = 'completed';

    public const VALID_STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_CONFIRMED,
        self::STATUS_CANCELLED,
        self::STATUS_COMPLETED,
    ];

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

        // customer_name
        $name = isset($input['customer_name']) ? trim((string) $input['customer_name']) : '';
        if ($name === '') {
            $errors['customer_name'] = 'Your name is required.';
        } elseif (mb_strlen($name) > 100) {
            $errors['customer_name'] = 'Name must be 100 characters or less.';
        }

        // phone
        $phone = isset($input['phone']) ? trim((string) $input['phone']) : '';
        if ($phone === '') {
            $errors['phone'] = 'A phone number is required.';
        } elseif (mb_strlen($phone) > 30) {
            $errors['phone'] = 'Phone number must be 30 characters or less.';
        }

        // email
        $email = isset($input['email']) ? trim((string) $input['email']) : '';
        if ($email === '') {
            $errors['email'] = 'An email address is required.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'The email address is not valid.';
        } elseif (mb_strlen($email) > 100) {
            $errors['email'] = 'Email must be 100 characters or less.';
        }

        // reservation_date
        $date = isset($input['reservation_date']) ? trim((string) $input['reservation_date']) : '';
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            $errors['reservation_date'] = 'Please choose a reservation date (YYYY-MM-DD).';
        } else {
            $parsed = DateTimeImmutable::createFromFormat('Y-m-d', $date);
            $today = new DateTimeImmutable('today');
            if (!$parsed instanceof DateTimeImmutable || $parsed->format('Y-m-d') !== $date) {
                $errors['reservation_date'] = 'The reservation date is not a valid date.';
            } elseif ($parsed < $today) {
                $errors['reservation_date'] = 'The reservation date cannot be in the past.';
            }
        }

        // reservation_time
        $time = isset($input['reservation_time']) ? trim((string) $input['reservation_time']) : '';
        if (!preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d$/', $time)) {
            $errors['reservation_time'] = 'Please choose a reservation time (HH:MM).';
        } elseif (isset($errors['reservation_date']) === false && !self::isWithinOpeningHours($date, $time)) {
            $errors['reservation_time'] = 'We are open Monday - Saturday 9am - 8pm, Sunday 9am - 6pm.';
        }

        // guests
        $guests = filter_var(
            $input['guests'] ?? null,
            FILTER_VALIDATE_INT,
            ['options' => ['min_range' => 1, 'max_range' => 20]]
        );
        if ($guests === false) {
            $errors['guests'] = 'Number of guests must be between 1 and 20.';
        }

        // message (optional)
        $message = isset($input['message']) ? trim((string) $input['message']) : '';
        if (mb_strlen($message) > 1000) {
            $errors['message'] = 'Message must be 1000 characters or less.';
        }

        return $errors;
    }

    /**
     * Insert a new reservation. Returns the new row id.
     *
     * @param array<string, mixed> $input validated input
     */
    public function create(array $input): int
    {
        $sql = 'INSERT INTO reservations
                    (customer_name, phone, email, reservation_date, reservation_time, guests, message, status)
                VALUES
                    (:customer_name, :phone, :email, :reservation_date, :reservation_time, :guests, :message, :status)';

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':customer_name'     => trim((string) $input['customer_name']),
            ':phone'             => trim((string) $input['phone']),
            ':email'             => trim((string) $input['email']),
            ':reservation_date'  => (string) $input['reservation_date'],
            ':reservation_time'  => (string) $input['reservation_time'],
            ':guests'            => (int) $input['guests'],
            ':message'           => isset($input['message']) ? trim((string) $input['message']) : '',
            ':status'            => self::STATUS_PENDING,
        ]);

        return (int) $this->db->lastInsertId();
    }

    /**
     * List all reservations (intended for the site owner).
     *
     * @return array<int, array<string, mixed>>
     */
    public function list(): array
    {
        $stmt = $this->db->query(
            'SELECT id, customer_name, phone, email, reservation_date, reservation_time,
                    guests, message, status, created_at, updated_at
             FROM reservations
             ORDER BY reservation_date DESC, reservation_time DESC'
        );

        return $stmt->fetchAll();
    }

    private static function isWithinOpeningHours(string $date, string $time): bool
    {
        [$hour, $minute] = array_map('intval', explode(':', $time));
        $minutes = ($hour * 60) + $minute;

        // Monday - Saturday: 09:00 - 20:00
        // Sunday:           09:00 - 18:00
        $isSunday = false;
        try {
            $isSunday = (new DateTimeImmutable($date))->format('N') === '7';
        } catch (Exception) {
            $isSunday = false;
        }

        $closing = $isSunday ? 18 * 60 : 20 * 60;

        return $minutes >= 9 * 60 && $minutes <= $closing;
    }
}