-- MySQL Script generated by MySQL Workbench
-- Wed Sep 20 10:59:52 2017
-- Model: New Model    Version: 1.0
-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Schema secretsharing
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema secretsharing
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `secretsharing` DEFAULT CHARACTER SET utf8 ;
USE `secretsharing` ;

-- -----------------------------------------------------
-- Table `secretsharing`.`Usuario`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `secretsharing`.`Usuario` (
  `idUsuario` VARCHAR(255) NOT NULL,
  `contrasenia` VARCHAR(255) NOT NULL,
  `alias` VARCHAR(45) NOT NULL,
  `status` TINYINT NOT NULL,
  `espacioUtilizado` INT NOT NULL,
  PRIMARY KEY (`idUsuario`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `secretsharing`.`Carpeta`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `secretsharing`.`Carpeta` (
  `idCarpeta` INT NOT NULL AUTO_INCREMENT,
  `idUsuario` VARCHAR(255) NOT NULL,
  `idCarpetaSuperior` INT NULL,
  `nombreCarpeta` VARCHAR(255) NOT NULL,
  `fechaCreacion` DATE NOT NULL,
  PRIMARY KEY (`idCarpeta`, `idUsuario`),
  INDEX `fk_Carpeta_Carpeta_idx` (`idCarpetaSuperior` ASC),
  INDEX `fk_Carpeta_Usuario1_idx` (`idUsuario` ASC),
  CONSTRAINT `fk_Carpeta_Carpeta`
    FOREIGN KEY (`idCarpetaSuperior`)
    REFERENCES `secretsharing`.`Carpeta` (`idCarpeta`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_Carpeta_Usuario1`
    FOREIGN KEY (`idUsuario`)
    REFERENCES `secretsharing`.`Usuario` (`idUsuario`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `secretsharing`.`Archivo`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `secretsharing`.`Archivo` (
  `nombreArchivo` VARCHAR(255) NOT NULL,
  `idCarpeta` INT NOT NULL,
  `idUsuario` VARCHAR(255) NOT NULL,
  `nombreArchivoGRID` VARCHAR(300) NOT NULL,
  `tamanio` INT NOT NULL,
  `fechaSubida` DATE NOT NULL,
  PRIMARY KEY (`nombreArchivo`, `idCarpeta`, `idUsuario`),
  INDEX `fk_Archivo_Carpeta1_idx` (`idCarpeta` ASC, `idUsuario` ASC),
  CONSTRAINT `fk_Archivo_Carpeta1`
    FOREIGN KEY (`idCarpeta` , `idUsuario`)
    REFERENCES `secretsharing`.`Carpeta` (`idCarpeta` , `idUsuario`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;