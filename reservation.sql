CREATE TABLE reservation (
    id INT AUTO_INCREMENT PRIMARY KEY,
    passenger_name VARCHAR(100) NOT NULL,
    schedule_code VARCHAR(50) NOT NULL,
    train_number VARCHAR(20) NOT NULL,
    seat_number VARCHAR(10) NOT NULL,
    departure_date DATE NOT NULL,
    departure_time TIME NOT NULL,
    status ENUM('confirmed', 'pending', 'cancelled') NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO reservation (
    passenger_name, schedule_code, train_number, seat_number, departure_date, departure_time, status
) VALUES 
('John Doe', 'TR-2023-001', 'T-101', 'B-12', '2023-12-15', '08:30:00', 'confirmed'),
('Jane Smith', 'TR-2023-002', 'T-102', 'A-05', '2023-12-16', '14:15:00', 'pending'),
('Robert Johnson', 'TR-2023-003', 'T-103', 'C-22', '2023-12-17', '10:45:00', 'confirmed'),
('Emily Davis', 'TR-2023-004', 'T-104', 'D-08', '2023-12-18', '16:20:00', 'cancelled');
