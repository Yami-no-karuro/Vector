CREATE TABLE IF NOT EXISTS `assets` (
    `ID` INT NOT NULL AUTO_INCREMENT ,
    `path` VARCHAR(250) NOT NULL UNIQUE ,
    `createdAt` BIGINT NOT NULL ,
    `modifiedAt` BIGINT NOT NULL ,
    `mimeType` VARCHAR(150) ,
    `size` BIGINT ,
    PRIMARY KEY (`ID`)
) ENGINE = InnoDB;