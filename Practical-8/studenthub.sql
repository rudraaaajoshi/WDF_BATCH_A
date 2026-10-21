CREATE DATABASE IF NOT EXISTS studenthub
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE studenthub;

CREATE TABLE IF NOT EXISTS students (
    student_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS events (
    event_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    category VARCHAR(50) NOT NULL,
    event_date DATE NOT NULL,
    location VARCHAR(150) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS registrations (
    registration_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    student_id INT UNSIGNED NOT NULL,
    event_id INT UNSIGNED NOT NULL,
    registered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_registrations_student
        FOREIGN KEY (student_id) REFERENCES students(student_id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_registrations_event
        FOREIGN KEY (event_id) REFERENCES events(event_id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT uq_student_event UNIQUE (student_id, event_id)
) ENGINE=InnoDB;

INSERT IGNORE INTO students (student_id, full_name, email) VALUES
(1, 'Aarav Patel', 'aarav@example.com'),
(2, 'Diya Shah', 'diya@example.com'),
(3, 'Rohan Mehta', 'rohan@example.com');

INSERT IGNORE INTO events (event_id, title, category, event_date, location) VALUES
(1, 'Web Development Workshop', 'Workshop', '2026-11-10', 'Computer Lab'),
(2, 'Coding Competition', 'Competition', '2026-11-12', 'Auditorium'),
(3, 'Technical Seminar', 'Seminar', '2026-11-15', 'Seminar Hall');

INSERT IGNORE INTO registrations (student_id, event_id) VALUES
(1, 1),
(2, 2),
(3, 1);
