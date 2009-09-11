SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

CREATE SCHEMA IF NOT EXISTS `pmb` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ;
USE `pmb`;

-- -----------------------------------------------------
-- Table `pmb`.`user`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `pmb`.`user` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `firstname` VARCHAR(255) NOT NULL ,
  `secondname` VARCHAR(255) NOT NULL ,
  `birthdate` DATE NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;


-- -----------------------------------------------------
-- Table `pmb`.`property`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `pmb`.`property` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `key` VARCHAR(255) NOT NULL ,
  `value` LONGTEXT NOT NULL ,
  `user_id` INT UNSIGNED NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_property_user` (`user_id` ASC) ,
  CONSTRAINT `fk_property_user`
    FOREIGN KEY (`user_id` )
    REFERENCES `pmb`.`user` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;



SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
