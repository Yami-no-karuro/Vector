CREATE TABLE IF NOT EXISTS `assets` (
    `ID` INT NOT NULL AUTO_INCREMENT ,
    `path` VARCHAR(50) NOT NULL UNIQUE ,
    `modified_at` BIGINT NOT NULL ,
    `mimetype` VARCHAR(150) ,
    `size` BIGINT ,
    PRIMARY KEY (`ID`)
) ENGINE = InnoDB;