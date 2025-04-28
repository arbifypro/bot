CREATE TABLE `payments` (
                            `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                            `card` VARCHAR(255) NOT NULL,
                            `type` VARCHAR(255) NOT NULL,
                            `amount` DECIMAL(10,2) NOT NULL,
                            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
