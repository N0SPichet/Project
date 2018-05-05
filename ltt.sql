-- MySQL Script generated by MySQL Workbench
-- Tue Apr 24 22:06:59 2018
-- Model: New Model    Version: 1.0
-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Schema LTT
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema LTT
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `LTT` DEFAULT CHARACTER SET utf8 ;
USE `LTT` ;

-- -----------------------------------------------------
-- Table `LTT`.`user_verifications`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `LTT`.`user_verifications` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `verify` INT NULL DEFAULT 0,
  `title` VARCHAR(45) NULL,
  `name` VARCHAR(100) NULL,
  `lastname` VARCHAR(100) NULL,
  `id_card` VARCHAR(50) NULL,
  `census_registration` VARCHAR(50) NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `LTT`.`users`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `LTT`.`users` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `level` INT NULL DEFAULT 1,
  `user_fname` VARCHAR(100) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL DEFAULT NULL,
  `user_lname` VARCHAR(100) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL DEFAULT NULL,
  `user_tel` VARCHAR(100) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL DEFAULT NULL,
  `user_gender` VARCHAR(100) NULL DEFAULT NULL,
  `user_address` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL DEFAULT NULL,
  `user_city` VARCHAR(50) NULL DEFAULT NULL,
  `user_state` VARCHAR(50) NULL DEFAULT NULL,
  `user_country` VARCHAR(50) NULL DEFAULT NULL,
  `user_description` TEXT CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL DEFAULT NULL,
  `user_score` FLOAT NULL DEFAULT 10,
  `user_image` VARCHAR(50) NULL DEFAULT NULL,
  `email` VARCHAR(100) NULL,
  `password` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL,
  `remember_token` VARCHAR(100) NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  `user_verifications_id` INT NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `email_UNIQUE` (`email` ASC),
  INDEX `fk_users_user_verifications1_idx` (`user_verifications_id` ASC),
  CONSTRAINT `fk_users_user_verifications1`
    FOREIGN KEY (`user_verifications_id`)
    REFERENCES `LTT`.`user_verifications` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


-- -----------------------------------------------------
-- Table `LTT`.`housetypes`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `LTT`.`housetypes` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `type_name` VARCHAR(255) NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `LTT`.`addresscountries`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `LTT`.`addresscountries` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `country_name` VARCHAR(45) NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `LTT`.`addressstates`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `LTT`.`addressstates` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `state_name` VARCHAR(45) NULL,
  `addresscountry_id` INT NOT NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_addressstates_addresscountries1_idx` (`addresscountry_id` ASC),
  CONSTRAINT `fk_addressstates_addresscountries1`
    FOREIGN KEY (`addresscountry_id`)
    REFERENCES `LTT`.`addresscountries` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `LTT`.`addresscities`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `LTT`.`addresscities` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `city_name` VARCHAR(45) NULL,
  `addressstate_id` INT NOT NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_addresscities_addressstates1_idx` (`addressstate_id` ASC),
  CONSTRAINT `fk_addresscities_addressstates1`
    FOREIGN KEY (`addressstate_id`)
    REFERENCES `LTT`.`addressstates` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `LTT`.`guestarrives`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `LTT`.`guestarrives` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `notice` VARCHAR(50) NULL DEFAULT NULL,
  `checkin_from` VARCHAR(50) NULL DEFAULT NULL,
  `checkin_to` VARCHAR(50) NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `LTT`.`houseprices`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `LTT`.`houseprices` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `price_perperson` INT NULL,
  `price` INT NULL DEFAULT NULL,
  `food_price` INT NULL,
  `welcome_offer` INT NULL DEFAULT NULL,
  `weekly_discount` INT NULL DEFAULT NULL,
  `monthly_discount` INT NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `LTT`.`foods`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `LTT`.`foods` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `breakfast` INT NULL,
  `lunch` INT NULL,
  `dinner` INT NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `LTT`.`apartmentprices`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `LTT`.`apartmentprices` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `type_single` INT NULL,
  `single_price` INT NULL,
  `type_deluxe_single` INT NULL,
  `deluxe_single_price` INT NULL,
  `type_double_room` INT NULL,
  `double_price` INT NULL,
  `discount` INT NULL,
  `welcome_offer` INT NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `LTT`.`houses`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `LTT`.`houses` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `publish` INT NULL,
  `house_title` VARCHAR(100) NULL DEFAULT NULL,
  `house_capacity` INT NULL DEFAULT NULL,
  `house_property` VARCHAR(50) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL DEFAULT NULL,
  `no_rooms` INT NULL DEFAULT NULL,
  `house_guestspace` ENUM('Entrie', 'Private', 'Shared') CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL DEFAULT NULL,
  `house_bedrooms` INT NULL DEFAULT NULL,
  `house_beds` INT NULL DEFAULT NULL,
  `house_bathroom` INT NULL DEFAULT NULL,
  `house_bathroomprivate` VARCHAR(100) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL DEFAULT NULL,
  `house_address` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL DEFAULT NULL,
  `house_postcode` INT NULL DEFAULT NULL,
  `house_description` TEXT NULL,
  `about_your_place` TEXT NULL DEFAULT NULL,
  `guest_can_access` TEXT NULL DEFAULT NULL,
  `optional_note` TEXT NULL DEFAULT NULL,
  `optional_rules` TEXT NULL DEFAULT NULL,
  `about_neighborhood` TEXT NULL DEFAULT NULL,
  `cover_image` VARCHAR(100) NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  `users_id` INT NOT NULL,
  `housetypes_id` INT NOT NULL,
  `addresscities_id` INT NOT NULL,
  `addressstates_id` INT NOT NULL,
  `addresscountries_id` INT NOT NULL,
  `guestarrives_id` INT NOT NULL,
  `houseprices_id` INT NULL DEFAULT NULL,
  `foods_id` INT NULL,
  `apartmentprices_id` INT NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_house_users1_idx` (`users_id` ASC),
  INDEX `fk_houses_housetypes1_idx` (`housetypes_id` ASC),
  INDEX `fk_houses_addresscities1_idx` (`addresscities_id` ASC),
  INDEX `fk_houses_addressstates1_idx` (`addressstates_id` ASC),
  INDEX `fk_houses_addresscountries1_idx` (`addresscountries_id` ASC),
  INDEX `fk_houses_guestarrives1_idx` (`guestarrives_id` ASC),
  INDEX `fk_houses_houseprices1_idx` (`houseprices_id` ASC),
  INDEX `fk_houses_foods1_idx` (`foods_id` ASC),
  INDEX `fk_houses_apartmentprices1_idx` (`apartmentprices_id` ASC),
  CONSTRAINT `fk_house_users1`
    FOREIGN KEY (`users_id`)
    REFERENCES `LTT`.`users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_houses_housetypes1`
    FOREIGN KEY (`housetypes_id`)
    REFERENCES `LTT`.`housetypes` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_houses_addresscities1`
    FOREIGN KEY (`addresscities_id`)
    REFERENCES `LTT`.`addresscities` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_houses_addressstates1`
    FOREIGN KEY (`addressstates_id`)
    REFERENCES `LTT`.`addressstates` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_houses_addresscountries1`
    FOREIGN KEY (`addresscountries_id`)
    REFERENCES `LTT`.`addresscountries` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_houses_guestarrives1`
    FOREIGN KEY (`guestarrives_id`)
    REFERENCES `LTT`.`guestarrives` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_houses_houseprices1`
    FOREIGN KEY (`houseprices_id`)
    REFERENCES `LTT`.`houseprices` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_houses_foods1`
    FOREIGN KEY (`foods_id`)
    REFERENCES `LTT`.`foods` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_houses_apartmentprices1`
    FOREIGN KEY (`apartmentprices_id`)
    REFERENCES `LTT`.`apartmentprices` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


-- -----------------------------------------------------
-- Table `LTT`.`payments`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `LTT`.`payments` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `payment_bankname` VARCHAR(45) NULL DEFAULT NULL,
  `payment_bankaccount` VARCHAR(45) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL DEFAULT NULL,
  `payment_holder` VARCHAR(45) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL DEFAULT NULL,
  `payment_amount` INT NULL,
  `payment_transfer_slip` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL DEFAULT NULL,
  `payment_status` VARCHAR(45) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `LTT`.`rentals`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `LTT`.`rentals` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `host_decision` VARCHAR(45) NULL,
  `rental_datein` DATE NULL,
  `rental_dateout` DATE NULL,
  `rental_guest` INT NULL,
  `no_type_single` INT NULL,
  `type_single_price` INT NULL,
  `no_type_deluxe_single` INT NULL,
  `type_deluxe_single_price` INT NULL,
  `no_type_double_room` INT NULL,
  `type_double_room_price` INT NULL,
  `no_rooms` INT NULL,
  `inc_food` INT NULL DEFAULT 0,
  `discount` INT NULL DEFAULT 0,
  `checkin_status` INT NULL DEFAULT 0,
  `checkincode` VARCHAR(45) NULL,
  `rental_checkroom` INT NULL DEFAULT 0,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  `users_id` INT NOT NULL,
  `houses_id` INT NOT NULL,
  `payments_id` INT NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_rentals_users1_idx` (`users_id` ASC),
  INDEX `fk_rentals_houses1_idx` (`houses_id` ASC),
  INDEX `fk_rentals_payments1_idx` (`payments_id` ASC),
  CONSTRAINT `fk_rentals_users1`
    FOREIGN KEY (`users_id`)
    REFERENCES `LTT`.`users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_rentals_houses1`
    FOREIGN KEY (`houses_id`)
    REFERENCES `LTT`.`houses` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_rentals_payments1`
    FOREIGN KEY (`payments_id`)
    REFERENCES `LTT`.`payments` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `LTT`.`categories`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `LTT`.`categories` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `category_name` VARCHAR(45) NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `LTT`.`diaries`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `LTT`.`diaries` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `publish` INT NOT NULL DEFAULT 0,
  `title` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL,
  `message` TEXT CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL,
  `days` INT NULL,
  `cover_image` VARCHAR(100) NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  `users_id` INT NOT NULL,
  `categories_id` INT NOT NULL,
  `rentals_id` INT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_diaries_users1_idx` (`users_id` ASC),
  INDEX `fk_diaries_categories1_idx` (`categories_id` ASC),
  INDEX `fk_diaries_rentals1_idx` (`rentals_id` ASC),
  CONSTRAINT `fk_diaries_users1`
    FOREIGN KEY (`users_id`)
    REFERENCES `LTT`.`users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_diaries_categories1`
    FOREIGN KEY (`categories_id`)
    REFERENCES `LTT`.`categories` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_diaries_rentals1`
    FOREIGN KEY (`rentals_id`)
    REFERENCES `LTT`.`rentals` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `LTT`.`houserules`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `LTT`.`houserules` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `houserule_name` VARCHAR(255) NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `LTT`.`tags`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `LTT`.`tags` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `tag_name` VARCHAR(45) NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `LTT`.`diary_tag`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `LTT`.`diary_tag` (
  `diary_id` INT NOT NULL,
  `tag_id` INT NOT NULL,
  PRIMARY KEY (`diary_id`, `tag_id`),
  INDEX `fk_diaries_has_tags_tags1_idx` (`tag_id` ASC),
  INDEX `fk_diaries_has_tags_diaries1_idx` (`diary_id` ASC),
  CONSTRAINT `fk_diaries_has_tags_diaries1`
    FOREIGN KEY (`diary_id`)
    REFERENCES `LTT`.`diaries` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_diaries_has_tags_tags1`
    FOREIGN KEY (`tag_id`)
    REFERENCES `LTT`.`tags` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `LTT`.`house_houserule`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `LTT`.`house_houserule` (
  `house_id` INT NOT NULL,
  `houserule_id` INT NOT NULL,
  PRIMARY KEY (`house_id`, `houserule_id`),
  INDEX `fk_houses_has_houserules_houserules1_idx` (`houserule_id` ASC),
  INDEX `fk_houses_has_houserules_houses1_idx` (`house_id` ASC),
  CONSTRAINT `fk_houses_has_houserules_houses1`
    FOREIGN KEY (`house_id`)
    REFERENCES `LTT`.`houses` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_houses_has_houserules_houserules1`
    FOREIGN KEY (`houserule_id`)
    REFERENCES `LTT`.`houserules` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


-- -----------------------------------------------------
-- Table `LTT`.`images`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `LTT`.`images` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `image_name` VARCHAR(50) NULL,
  `room_type` INT NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  `houses_id` INT NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_images_houses1_idx` (`houses_id` ASC),
  CONSTRAINT `fk_images_houses1`
    FOREIGN KEY (`houses_id`)
    REFERENCES `LTT`.`houses` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `LTT`.`houseamenities`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `LTT`.`houseamenities` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `amenityname` VARCHAR(100) NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `LTT`.`housedetails`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `LTT`.`housedetails` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `must_know` VARCHAR(50) NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `LTT`.`house_housedetail`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `LTT`.`house_housedetail` (
  `house_id` INT NOT NULL,
  `housedetail_id` INT NOT NULL,
  PRIMARY KEY (`house_id`, `housedetail_id`),
  INDEX `fk_houses_has_housedetails_housedetails1_idx` (`housedetail_id` ASC),
  INDEX `fk_houses_has_housedetails_houses1_idx` (`house_id` ASC),
  CONSTRAINT `fk_houses_has_housedetails_houses1`
    FOREIGN KEY (`house_id`)
    REFERENCES `LTT`.`houses` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_houses_has_housedetails_housedetails1`
    FOREIGN KEY (`housedetail_id`)
    REFERENCES `LTT`.`housedetails` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


-- -----------------------------------------------------
-- Table `LTT`.`house_houseamenity`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `LTT`.`house_houseamenity` (
  `house_id` INT NOT NULL,
  `houseamenity_id` INT NOT NULL,
  PRIMARY KEY (`house_id`, `houseamenity_id`),
  INDEX `fk_houses_has_houseamenities_houseamenities1_idx` (`houseamenity_id` ASC),
  INDEX `fk_houses_has_houseamenities_houses1_idx` (`house_id` ASC),
  CONSTRAINT `fk_houses_has_houseamenities_houses1`
    FOREIGN KEY (`house_id`)
    REFERENCES `LTT`.`houses` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_houses_has_houseamenities_houseamenities1`
    FOREIGN KEY (`houseamenity_id`)
    REFERENCES `LTT`.`houseamenities` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


-- -----------------------------------------------------
-- Table `LTT`.`housespaces`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `LTT`.`housespaces` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `spacename` VARCHAR(100) NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `LTT`.`house_housespace`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `LTT`.`house_housespace` (
  `house_id` INT NOT NULL,
  `housespace_id` INT NOT NULL,
  PRIMARY KEY (`house_id`, `housespace_id`),
  INDEX `fk_houses_has_housespaces_housespaces1_idx` (`housespace_id` ASC),
  INDEX `fk_houses_has_housespaces_houses1_idx` (`house_id` ASC),
  CONSTRAINT `fk_houses_has_housespaces_houses1`
    FOREIGN KEY (`house_id`)
    REFERENCES `LTT`.`houses` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_houses_has_housespaces_housespaces1`
    FOREIGN KEY (`housespace_id`)
    REFERENCES `LTT`.`housespaces` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


-- -----------------------------------------------------
-- Table `LTT`.`comments`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `LTT`.`comments` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NULL,
  `email` VARCHAR(100) NULL,
  `comment` TEXT NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  `diary_id` INT NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_comments_diaries1_idx` (`diary_id` ASC),
  CONSTRAINT `fk_comments_diaries1`
    FOREIGN KEY (`diary_id`)
    REFERENCES `LTT`.`diaries` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `LTT`.`password_resets`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `LTT`.`password_resets` (
  `email` VARCHAR(255) NOT NULL,
  `token` VARCHAR(255) NULL,
  `created_at` TIMESTAMP NULL,
  PRIMARY KEY (`email`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `LTT`.`room_reviews`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `LTT`.`room_reviews` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `clean` INT NULL,
  `amenity` INT NULL,
  `service` INT NULL,
  `host` INT NULL,
  `comment` TEXT NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  `user_id` INT NOT NULL,
  `house_id` INT NOT NULL,
  `rental_id` INT NOT NULL,
  PRIMARY KEY (`id`, `user_id`, `house_id`, `rental_id`),
  INDEX `fk_room_reviews_users1_idx` (`user_id` ASC),
  INDEX `fk_room_reviews_houses1_idx` (`house_id` ASC),
  INDEX `fk_room_reviews_rentals1_idx` (`rental_id` ASC),
  CONSTRAINT `fk_room_reviews_users1`
    FOREIGN KEY (`user_id`)
    REFERENCES `LTT`.`users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_room_reviews_houses1`
    FOREIGN KEY (`house_id`)
    REFERENCES `LTT`.`houses` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_room_reviews_rentals1`
    FOREIGN KEY (`rental_id`)
    REFERENCES `LTT`.`rentals` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `LTT`.`maps`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `LTT`.`maps` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `map_name` VARCHAR(100) NULL,
  `map_lat` FLOAT NULL,
  `map_lng` FLOAT NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  `houses_id` INT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_maps_houses1_idx` (`houses_id` ASC),
  CONSTRAINT `fk_maps_houses1`
    FOREIGN KEY (`houses_id`)
    REFERENCES `LTT`.`houses` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `LTT`.`diary_images`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `LTT`.`diary_images` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `image` VARCHAR(100) NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  `diary_id` INT NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_diary_images_diaries1_idx` (`diary_id` ASC),
  CONSTRAINT `fk_diary_images_diaries1`
    FOREIGN KEY (`diary_id`)
    REFERENCES `LTT`.`diaries` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `LTT`.`subscribes`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `LTT`.`subscribes` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `writer` INT NULL,
  `follower` INT NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
