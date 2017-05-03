-- MySQL Script generated by MySQL Workbench
-- Tue May  2 22:36:47 2017
-- Model: New Model    Version: 1.0
-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Schema accueil_ferme
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Table `adress`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `adress` ;

CREATE TABLE IF NOT EXISTS `adress` (
  `pk` INT NOT NULL AUTO_INCREMENT,
  `street` VARCHAR(255) NULL,
  `postal_code` VARCHAR(20) NULL,
  `ville` VARCHAR(105) NULL,
  `region` VARCHAR(105) NULL,
  `country` VARCHAR(105) NULL,
  PRIMARY KEY (`pk`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `person`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `person` ;

CREATE TABLE IF NOT EXISTS `person` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `firstname` VARCHAR(105) NULL,
  `lastname` VARCHAR(105) NULL,
  `birthday` DATE NULL,
  `email` VARCHAR(255) NULL,
  `adresse_pk` INT NULL,
  `comment` TEXT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_personne_adresse1_idx` (`adresse_pk` ASC),
  CONSTRAINT `fk_personne_adresse1`
    FOREIGN KEY (`adresse_pk`)
    REFERENCES `adress` (`pk`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `family`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `family` ;

CREATE TABLE IF NOT EXISTS `family` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(105) NULL,
  `adress_pk` INT NULL,
  `comment` TEXT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_famille_adresse1_idx` (`adress_pk` ASC),
  CONSTRAINT `fk_famille_adresse1`
    FOREIGN KEY (`adress_pk`)
    REFERENCES `adress` (`pk`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `family_link`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `family_link` ;

CREATE TABLE IF NOT EXISTS `family_link` (
  `pk` INT NOT NULL AUTO_INCREMENT,
  `slug` VARCHAR(45) NULL,
  `name` VARCHAR(45) NULL,
  PRIMARY KEY (`pk`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `person_has_family`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `person_has_family` ;

CREATE TABLE IF NOT EXISTS `person_has_family` (
  `person_id` INT NOT NULL,
  `family_id` INT NOT NULL,
  `is_member` TINYINT(1) NULL,
  `family_link_pk` INT NOT NULL,
  PRIMARY KEY (`person_id`, `family_id`),
  INDEX `fk_personnes_has_famille_famille1_idx` (`family_id` ASC),
  INDEX `fk_personnes_has_famille_personnes_idx` (`person_id` ASC),
  INDEX `fk_personne_has_famille_lien_famille1_idx` (`family_link_pk` ASC),
  CONSTRAINT `fk_personnes_has_famille_personnes`
    FOREIGN KEY (`person_id`)
    REFERENCES `person` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_personnes_has_famille_famille1`
    FOREIGN KEY (`family_id`)
    REFERENCES `family` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_personne_has_famille_lien_famille1`
    FOREIGN KEY (`family_link_pk`)
    REFERENCES `family_link` (`pk`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `event_cat`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `event_cat` ;

CREATE TABLE IF NOT EXISTS `event_cat` (
  `pk` INT NOT NULL AUTO_INCREMENT,
  `slug` VARCHAR(45) NULL,
  `nom` VARCHAR(255) NULL,
  `description` TEXT NULL,
  PRIMARY KEY (`pk`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `event`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `event` ;

CREATE TABLE IF NOT EXISTS `event` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `slug` VARCHAR(105) NULL,
  `nom` VARCHAR(255) NULL,
  `debut` DATETIME NULL,
  `fin` DATETIME NULL,
  `description` TEXT NULL,
  `event_cat_pk` INT NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_event_event_cat1_idx` (`event_cat_pk` ASC),
  CONSTRAINT `fk_event_event_cat1`
    FOREIGN KEY (`event_cat_pk`)
    REFERENCES `event_cat` (`pk`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `paiement`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `paiement` ;

CREATE TABLE IF NOT EXISTS `paiement` (
  `pk` INT NOT NULL AUTO_INCREMENT,
  `personne_id` INT NOT NULL,
  `date` DATETIME NULL,
  `commentaire` TEXT NULL,
  PRIMARY KEY (`pk`),
  INDEX `fk_inscription_has_personne_personne1_idx` (`personne_id` ASC),
  CONSTRAINT `fk_inscription_has_personne_personne1`
    FOREIGN KEY (`personne_id`)
    REFERENCES `person` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `type_inscription`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `type_inscription` ;

CREATE TABLE IF NOT EXISTS `type_inscription` (
  `pk` INT NOT NULL AUTO_INCREMENT,
  `slug` VARCHAR(45) NULL,
  `nom` VARCHAR(255) NULL,
  `description` TEXT NULL,
  `event_id` INT NOT NULL,
  `prix` FLOAT NULL,
  PRIMARY KEY (`pk`),
  INDEX `fk_type_inscription_event1_idx` (`event_id` ASC),
  CONSTRAINT `fk_type_inscription_event1`
    FOREIGN KEY (`event_id`)
    REFERENCES `event` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `inscription`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `inscription` ;

CREATE TABLE IF NOT EXISTS `inscription` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `arrivee` DATETIME NULL,
  `depart` DATETIME NULL,
  `personne_id` INT NOT NULL,
  `event_id` INT NOT NULL,
  `register_date` DATETIME NULL,
  `update_date` DATETIME NULL,
  `paiement_pk` INT NULL,
  `commentaire` TEXT NULL,
  `type_inscription_pk` INT NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_inscription_personne1_idx` (`personne_id` ASC),
  INDEX `fk_inscription_event1_idx` (`event_id` ASC),
  INDEX `fk_inscription_paiements1_idx` (`paiement_pk` ASC),
  INDEX `fk_inscription_type_inscription1_idx` (`type_inscription_pk` ASC),
  CONSTRAINT `fk_inscription_personne1`
    FOREIGN KEY (`personne_id`)
    REFERENCES `person` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_inscription_event1`
    FOREIGN KEY (`event_id`)
    REFERENCES `event` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_inscription_paiements1`
    FOREIGN KEY (`paiement_pk`)
    REFERENCES `paiement` (`pk`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_inscription_type_inscription1`
    FOREIGN KEY (`type_inscription_pk`)
    REFERENCES `type_inscription` (`pk`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `activite_role`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `activite_role` ;

CREATE TABLE IF NOT EXISTS `activite_role` (
  `pk` INT NOT NULL AUTO_INCREMENT,
  `slug` VARCHAR(45) NULL,
  `nom` VARCHAR(45) NULL,
  PRIMARY KEY (`pk`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `lieu_type`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `lieu_type` ;

CREATE TABLE IF NOT EXISTS `lieu_type` (
  `pk` INT NOT NULL AUTO_INCREMENT,
  `slug` VARCHAR(45) NULL,
  `nom` VARCHAR(255) NULL,
  `description` TEXT NULL,
  PRIMARY KEY (`pk`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `lieu`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `lieu` ;

CREATE TABLE IF NOT EXISTS `lieu` (
  `pk` INT NOT NULL AUTO_INCREMENT,
  `slug` VARCHAR(45) NULL,
  `nom` VARCHAR(255) NULL,
  `description` TEXT NULL,
  `lieu_type_pk` INT NOT NULL,
  `parent_pk` INT NOT NULL,
  `adresse_pk` INT NOT NULL,
  PRIMARY KEY (`pk`),
  INDEX `fk_hebergement_hebergement_type1_idx` (`lieu_type_pk` ASC),
  INDEX `fk_lieu_lieu1_idx` (`parent_pk` ASC),
  INDEX `fk_lieu_adresse1_idx` (`adresse_pk` ASC),
  CONSTRAINT `fk_hebergement_hebergement_type1`
    FOREIGN KEY (`lieu_type_pk`)
    REFERENCES `lieu_type` (`pk`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_lieu_lieu1`
    FOREIGN KEY (`parent_pk`)
    REFERENCES `lieu` (`pk`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_lieu_adresse1`
    FOREIGN KEY (`adresse_pk`)
    REFERENCES `adress` (`pk`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `activite`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `activite` ;

CREATE TABLE IF NOT EXISTS `activite` (
  `pk` INT NOT NULL AUTO_INCREMENT,
  `event_id` INT NULL,
  `slug` VARCHAR(105) NULL,
  `nom` VARCHAR(255) NULL,
  `description` TEXT NULL,
  `debut` DATETIME NULL,
  `fin` DATETIME NULL,
  `lieu_pk` INT NOT NULL,
  `is_public` TINYINT(1) NULL,
  `start_age` INT NULL,
  `end_age` INT NULL,
  INDEX `fk_event_has_benevolat_event1_idx` (`event_id` ASC),
  PRIMARY KEY (`pk`),
  INDEX `fk_activite_lieu1_idx` (`lieu_pk` ASC),
  CONSTRAINT `fk_event_has_benevolat_event1`
    FOREIGN KEY (`event_id`)
    REFERENCES `event` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_activite_lieu1`
    FOREIGN KEY (`lieu_pk`)
    REFERENCES `lieu` (`pk`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `activite_has_participant`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `activite_has_participant` ;

CREATE TABLE IF NOT EXISTS `activite_has_participant` (
  `activite_pk` INT NOT NULL,
  `inscription_id` INT NOT NULL,
  `activite_role_pk` INT NOT NULL,
  PRIMARY KEY (`activite_pk`, `inscription_id`),
  INDEX `fk_activite_has_inscription_inscription1_idx` (`inscription_id` ASC),
  INDEX `fk_activite_has_inscription_activite1_idx` (`activite_pk` ASC),
  INDEX `fk_activite_has_participant_activite_role1_idx` (`activite_role_pk` ASC),
  CONSTRAINT `fk_activite_has_inscription_activite1`
    FOREIGN KEY (`activite_pk`)
    REFERENCES `activite` (`pk`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_activite_has_inscription_inscription1`
    FOREIGN KEY (`inscription_id`)
    REFERENCES `inscription` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_activite_has_participant_activite_role1`
    FOREIGN KEY (`activite_role_pk`)
    REFERENCES `activite_role` (`pk`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `tag`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `tag` ;

CREATE TABLE IF NOT EXISTS `tag` (
  `pk` INT NOT NULL AUTO_INCREMENT,
  `slug` VARCHAR(45) NULL,
  `nom` VARCHAR(255) NULL,
  PRIMARY KEY (`pk`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `activite_has_tag`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `activite_has_tag` ;

CREATE TABLE IF NOT EXISTS `activite_has_tag` (
  `activite_pk` INT NOT NULL,
  `activite_tag_pk` INT NOT NULL,
  PRIMARY KEY (`activite_pk`, `activite_tag_pk`),
  INDEX `fk_activite_has_activite_tag_activite_tag1_idx` (`activite_tag_pk` ASC),
  INDEX `fk_activite_has_activite_tag_activite1_idx` (`activite_pk` ASC),
  CONSTRAINT `fk_activite_has_activite_tag_activite1`
    FOREIGN KEY (`activite_pk`)
    REFERENCES `activite` (`pk`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_activite_has_activite_tag_activite_tag1`
    FOREIGN KEY (`activite_tag_pk`)
    REFERENCES `tag` (`pk`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `inscription_has_hebergement`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `inscription_has_hebergement` ;

CREATE TABLE IF NOT EXISTS `inscription_has_hebergement` (
  `inscription_id` INT NOT NULL,
  `hebergement_pk` INT NOT NULL,
  `date_debut` DATETIME NULL,
  `date_fin` DATETIME NULL,
  PRIMARY KEY (`inscription_id`, `hebergement_pk`),
  INDEX `fk_inscription_has_hebergement_hebergement1_idx` (`hebergement_pk` ASC),
  INDEX `fk_inscription_has_hebergement_inscription1_idx` (`inscription_id` ASC),
  CONSTRAINT `fk_inscription_has_hebergement_inscription1`
    FOREIGN KEY (`inscription_id`)
    REFERENCES `inscription` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_inscription_has_hebergement_hebergement1`
    FOREIGN KEY (`hebergement_pk`)
    REFERENCES `lieu` (`pk`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
