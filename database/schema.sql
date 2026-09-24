-- ============================================================
-- Steakhouse Restaurant - MySQL Schema & Seed Data
-- ------------------------------------------------------------
-- Import with phpMyAdmin, or from the command line:
--
--   mysql -u root -p < database/schema.sql
--
-- On XAMPP the root user typically has an empty password:
--
--   "C:\xampp\mysql\bin\mysql.exe" -u root < database\schema.sql
--
-- Engine: MySQL 5.7+ / 8.x / MariaDB 10.4+ (all work)
-- Charset: utf8mb4 for full Unicode support.
-- ============================================================

CREATE DATABASE IF NOT EXISTS steakhouse
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE steakhouse;

-- ------------------------------------------------------------
-- Reservations
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS reservations (
    id                BIGINT UNSIGNED     NOT NULL AUTO_INCREMENT,
    customer_name     VARCHAR(100)        NOT NULL,
    phone             VARCHAR(30)         NOT NULL,
    email             VARCHAR(100)        NOT NULL,
    reservation_date  DATE                NOT NULL,
    reservation_time  TIME                NOT NULL,
    guests            TINYINT UNSIGNED    NOT NULL DEFAULT 1,
    message           VARCHAR(1000)       NOT NULL DEFAULT '',
    status            ENUM('pending','confirmed','cancelled','completed')
                                          NOT NULL DEFAULT 'pending',
    created_at        DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at        DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP
                                          ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    INDEX idx_reservations_date (reservation_date),
    INDEX idx_reservations_status (status),
    INDEX idx_reservations_email (email),
    CONSTRAINT chk_reservations_guests CHECK (guests BETWEEN 1 AND 20)
) ENGINE = InnoDB;

-- ------------------------------------------------------------
-- Contact messages
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS contact_messages (
    id          BIGINT UNSIGNED   NOT NULL AUTO_INCREMENT,
    name        VARCHAR(100)      NOT NULL,
    email       VARCHAR(100)      NOT NULL,
    phone       VARCHAR(30)       NOT NULL DEFAULT '',
    subject     VARCHAR(150)      NOT NULL,
    message     VARCHAR(2000)     NOT NULL,
    created_at  DATETIME          NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    INDEX idx_contact_created_at (created_at)
) ENGINE = InnoDB;

-- ------------------------------------------------------------
-- Menu items
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS menu_items (
    id            BIGINT UNSIGNED  NOT NULL AUTO_INCREMENT,
    name          VARCHAR(100)     NOT NULL,
    description   VARCHAR(500)     NOT NULL,
    price         DECIMAL(10,2)    NOT NULL,
    category      ENUM('starter','main','salad','dessert')
                                   NOT NULL,
    image         VARCHAR(255)     NOT NULL,
    is_available  TINYINT(1)       NOT NULL DEFAULT 1,
    created_at    DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at    DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP
                                   ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    INDEX idx_menu_category (category),
    INDEX idx_menu_available (is_available),
    CONSTRAINT chk_menu_price CHECK (price >= 0)
) ENGINE = InnoDB;

-- ------------------------------------------------------------
-- Seed data - menu items (matches the original static design)
-- ------------------------------------------------------------
INSERT INTO menu_items (name, description, price, category, image) VALUES
    ('Bruschetta', 'Start with our fresh baked bread with an egg and basil on top.', 180.00, 'starter', 'assets/img/menu-dish-1.png'),
    ('Main Dish', 'Our juicy, freshly grilled steak is served to satisfy your appetite.', 650.00, 'main', 'assets/img/menu-dish-2.png'),
    ('Salad Dish', 'Accompany your steak with a healthy salad mixed with sliced lean meat.', 260.00, 'salad', 'assets/img/menu-dish-3.png'),
    ('Dessert', 'End your culinary experience with a cake to cleanse your palate.', 150.00, 'dessert', 'assets/img/menu-dish-4.png');