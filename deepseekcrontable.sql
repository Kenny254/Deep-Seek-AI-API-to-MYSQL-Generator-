CREATE TABLE `gonews` (
  `id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `category` VARCHAR(32) NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL,
  `content` TEXT NOT NULL,
  `author_id` INT(11) DEFAULT NULL,
  `featured_image` VARCHAR(255) DEFAULT NULL,
  `is_top` TINYINT(1) DEFAULT 0,
  `status` ENUM('draft','published','archived') DEFAULT 'draft',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
