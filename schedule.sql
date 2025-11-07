CREATE TABLE `schedules` (
`schedule_id` INT AUTO_INCREMENT PRIMARY KEY,
`train_no` VARCHAR(20) NOT NULL,
`schedule_type` ENUM('daily', 'one-time') NOT NULL,
`route_from` VARCHAR(100) NOT NULL,
`route_to` VARCHAR(100) NOT NULL,
`departure_time` TIME NOT NULL,
`arrival_time` TIME NOT NULL,
`fare` DECIMAL(10,2) NOT NULL,
`capacity` INT NOT NULL,
`schedule_date` DATE DEFAULT NULL,
`days_of_week` VARCHAR(50) DEFAULT NULL
);
