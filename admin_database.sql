-- Database: `bioelectrode` (Assuming this is your database name based on context)
-- Or create if not exists:
-- CREATE DATABASE IF NOT EXISTS `bioelectrode`;
-- USE `bioelectrode`;

-- 1. Modify existing `users` table to support roles and status if it doesn't have them
-- (Assuming `users` table already exists with `id`, `name`, `email`, `password`, `created_at`)

ALTER TABLE `users` 
ADD COLUMN IF NOT EXISTS `role` ENUM('Student', 'Researcher', 'Educator', 'Admin') DEFAULT 'Student' AFTER `password`,
ADD COLUMN IF NOT EXISTS `status` ENUM('Pending', 'Active', 'Blocked') DEFAULT 'Pending' AFTER `role`,
ADD COLUMN IF NOT EXISTS `bio` TEXT DEFAULT NULL AFTER `status`,
ADD COLUMN IF NOT EXISTS `profile_image` VARCHAR(255) DEFAULT NULL AFTER `bio`,
ADD COLUMN IF NOT EXISTS `last_login` TIMESTAMP NULL DEFAULT NULL AFTER `created_at`;

-- 2. Create `datasets` table for managing uploaded datasets
CREATE TABLE IF NOT EXISTS `datasets` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `signal_type` ENUM('ECG', 'EEG', 'EMG') NOT NULL,
    `technique` ENUM('Bipolar', 'Monopolar') NOT NULL,
    `file_size` VARCHAR(50) NOT NULL,
    `file_path` VARCHAR(255) NOT NULL,
    `upload_date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `status` ENUM('Raw', 'Processed', 'Training') DEFAULT 'Raw',
    `uploaded_by` INT,
    FOREIGN KEY (`uploaded_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
);

-- 3. Create `ai_models` table for tracking trained models
CREATE TABLE IF NOT EXISTS `ai_models` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `version` VARCHAR(50) NOT NULL,
    `training_accuracy` DECIMAL(5,2) NOT NULL,
    `validation_score` DECIMAL(5,2) NOT NULL,
    `last_trained` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `status` ENUM('Development', 'Deployed', 'Archived') DEFAULT 'Development'
);

-- 4. Create `system_logs` table for recent activity
CREATE TABLE IF NOT EXISTS `system_logs` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT DEFAULT NULL,
    `action` VARCHAR(255) NOT NULL,
    `details` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
);

-- 5. Insert a default Admin user (Password is 'admin123' using password_hash)
-- REPLACE the password hash with your preferred secure hash or use simple string if testing (NOT RECOMMENDED for production)
-- For testing purposes, we might just insert a plain string if that's how your login.php handles it (usually password_verify is used)
-- Assuming password_hash('admin123', PASSWORD_BCRYPT) gives something like: $2y$10$w/kLEaZ/5R7r0N...

INSERT INTO `users` (`name`, `email`, `password`, `role`, `status`) 
VALUES ('Super Admin', 'admin@bioelectrode.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin', 'Active')
-- If the email already exists, just update it to be an Admin
ON DUPLICATE KEY UPDATE `role` = 'Admin', `status` = 'Active';

-- 6. Insert some Mock Data for Users
INSERT IGNORE INTO `users` (`name`, `email`, `password`, `role`, `status`) VALUES
('John Doe', 'john.student@example.com', 'hashed_pwd1', 'Student', 'Active'),
('Dr. Smith', 'smith.research@example.com', 'hashed_pwd2', 'Researcher', 'Pending'),
('Prof. Miller', 'miller.edu@example.com', 'hashed_pwd3', 'Educator', 'Active'),
('Jane Blocked', 'jane.blocked@example.com', 'hashed_pwd4', 'Student', 'Blocked');

-- 7. Insert some Mock Data for Datasets
INSERT IGNORE INTO `datasets` (`name`, `signal_type`, `technique`, `file_size`, `file_path`, `status`) VALUES
('Atrial Fibrillation DB', 'ECG', 'Bipolar', '124 MB', '/uploads/datasets/afib_db.csv', 'Processed'),
('Motor Imagery Data', 'EEG', 'Monopolar', '2.1 GB', '/uploads/datasets/motor_eeg.edf', 'Training'),
('Muscle Fatigue DB', 'EMG', 'Bipolar', '56 MB', '/uploads/datasets/fatigue_emg.csv', 'Raw');

-- 8. Insert initial AI Model stats
INSERT IGNORE INTO `ai_models` (`version`, `training_accuracy`, `validation_score`, `status`) VALUES
('v2.4.1', 94.80, 92.30, 'Deployed'),
('v2.5.0-beta', 96.10, 93.50, 'Development');

-- 8.5 Create `app_items` for general platform updates / content
CREATE TABLE IF NOT EXISTS `app_items` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(255) NOT NULL,
    `description` TEXT NOT NULL,
    `type` ENUM('Dataset', 'Model', 'Announcement', 'Feature') DEFAULT 'Announcement',
    `added_date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT IGNORE INTO `app_items` (`title`, `description`, `type`) VALUES
('New Simulator Feature', 'Enabled multi-channel comparison in simulator module.', 'Feature'),
('Bipolar Study Data Released', 'New curated dataset testing bipolar setup now available.', 'Dataset');

-- 9. Insert some mock logs
INSERT IGNORE INTO `system_logs` (`action`, `details`) VALUES
('System Boot', 'Server started successfully'),
('Dataset Upload', 'Atrial Fibrillation DB uploaded by system'),
('Model Training', 'v2.4.1 finished training with 94.8% accuracy');
