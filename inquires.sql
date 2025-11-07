CREATE TABLE `inquiries` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `inquirer_name` VARCHAR(100) NOT NULL,
    `inquirer_email` VARCHAR(100) NOT NULL,
    `message` TEXT NOT NULL,
    `timestamp` DATETIME DEFAULT CURRENT_TIMESTAMP
);
