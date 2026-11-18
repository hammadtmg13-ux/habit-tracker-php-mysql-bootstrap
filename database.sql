-- HabitTrack - Simple Habit Tracker
-- Database setup: run with: mysql -u root -p < database.sql

CREATE DATABASE IF NOT EXISTS habit_tracker CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE habit_tracker;

CREATE TABLE IF NOT EXISTS habits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    category ENUM('health', 'fitness', 'study', 'productivity', 'personal') NOT NULL DEFAULT 'personal',
    target_days INT NOT NULL DEFAULT 7,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS habit_checkins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    habit_id INT NOT NULL,
    checkin_date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_checkin (habit_id, checkin_date),
    FOREIGN KEY (habit_id) REFERENCES habits(id) ON DELETE CASCADE
);

-- Sample habits to get started
INSERT INTO habits (name, category, target_days) VALUES
('Drink 2 litres of water', 'health', 30),
('Read for 20 minutes', 'study', 21),
('Morning walk', 'fitness', 14),
('Practice coding for 1 hour', 'productivity', 30);
