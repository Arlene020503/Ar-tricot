-- Schéma minimal pour la base de données Ar Tricot
-- Table `orders` pour enregistrer les commandes passées depuis le site
CREATE TABLE IF NOT EXISTS `orders` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `product_code` VARCHAR(120) NOT NULL,
  `product_name` VARCHAR(255) DEFAULT NULL,
  `customer_name` VARCHAR(255) DEFAULT NULL,
  `contact` VARCHAR(255) DEFAULT NULL,
  `contact_type` VARCHAR(20) DEFAULT 'phone',
  `note` TEXT DEFAULT NULL,
  `size` VARCHAR(20) DEFAULT NULL,
  `color` VARCHAR(80) DEFAULT NULL,
  `delivery_address` TEXT DEFAULT NULL,
  `quantity` INT DEFAULT 1,
  `payment_method` VARCHAR(80) DEFAULT NULL,
  `status` VARCHAR(80) DEFAULT 'pending',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX (`product_code`)
);

-- Vous pouvez ajouter ici d'autres tables (products, reviews) si nécessaire
-- Table `reviews` pour stocker les avis clients
CREATE TABLE IF NOT EXISTS `reviews` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `contact` VARCHAR(255) DEFAULT NULL,
  `contact_type` VARCHAR(20) DEFAULT 'phone',
  `message` TEXT NOT NULL,
  `rating` INT DEFAULT 0,
  `rating_count` INT DEFAULT 0,
  `rating_sum` INT DEFAULT 0,
  `edit_token` VARCHAR(64) DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
);
