CREATE DATABASE IF NOT EXISTS sdn_db CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

USE sdn_db;

CREATE TABLE `users` (
  `user_id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(120) NOT NULL,
  `password_hash` VARCHAR(255) NOT NULL,
  `college_course` VARCHAR(150) NULL,
  `skills` VARCHAR(255) NULL,
  `bio` TEXT NULL,
  `role` ENUM('member','admin') NOT NULL DEFAULT 'member',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `uq_users_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `project_category` (
  `category_id` INT NOT NULL AUTO_INCREMENT,
  `category_name` VARCHAR(80) NOT NULL,
  `description` VARCHAR(255) NULL,
  PRIMARY KEY (`category_id`),
  UNIQUE KEY `uq_category_name` (`category_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `project` (
  `project_id` INT NOT NULL AUTO_INCREMENT,
  `owner_id` INT NOT NULL,
  `category_id` INT NOT NULL,
  `title` VARCHAR(120) NOT NULL,
  `description` TEXT NULL,
  `tech_stack` VARCHAR(255) NULL,
  `team_size_needed` INT NULL,
  `status` ENUM('open','in_progress','closed') NOT NULL DEFAULT 'open',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`project_id`),
  CONSTRAINT `fk_project_owner` FOREIGN KEY (`owner_id`)
    REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_project_category` FOREIGN KEY (`category_id`)
    REFERENCES `project_category` (`category_id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `project_application` (
  `application_id` INT NOT NULL AUTO_INCREMENT,
  `project_id` INT NOT NULL,
  `applicant_id` INT NOT NULL,
  `message` VARCHAR(255) NULL,
  `status` ENUM('pending','accepted','rejected','withdrawn') NOT NULL DEFAULT 'pending',
  `applied_on` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`application_id`),
  UNIQUE KEY `uq_application` (`project_id`, `applicant_id`),
  CONSTRAINT `fk_app_project` FOREIGN KEY (`project_id`)
    REFERENCES `project` (`project_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_app_user` FOREIGN KEY (`applicant_id`)
    REFERENCES `users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `event` (
  `event_id` INT NOT NULL AUTO_INCREMENT,
  `created_by` INT NOT NULL,
  `title` VARCHAR(120) NOT NULL,
  `description` TEXT NULL,
  `event_type` ENUM('event','workshop','hackathon') NOT NULL,
  `event_date` DATETIME NOT NULL,
  `venue` VARCHAR(150) NULL,
  `capacity` INT NULL,
  PRIMARY KEY (`event_id`),
  CONSTRAINT `fk_event_user` FOREIGN KEY (`created_by`)
    REFERENCES `users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `event_registration` (
  `registration_id` INT NOT NULL AUTO_INCREMENT,
  `event_id` INT NOT NULL,
  `user_id` INT NOT NULL,
  `registered_on` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`registration_id`),
  UNIQUE KEY `uq_registration` (`event_id`, `user_id`),
  CONSTRAINT `fk_reg_event` FOREIGN KEY (`event_id`)
    REFERENCES `event` (`event_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_reg_user` FOREIGN KEY (`user_id`)
    REFERENCES `users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `notification` (
  `notification_id` INT NOT NULL AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `message` VARCHAR(255) NOT NULL,
  `is_read` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`notification_id`),
  CONSTRAINT `fk_notif_user` FOREIGN KEY (`user_id`)
    REFERENCES `users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;