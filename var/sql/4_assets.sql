CREATE TABLE IF NOT EXISTS `assets` (
    `ID` INT NOT NULL AUTO_INCREMENT ,
    `path` VARCHAR(250) NOT NULL UNIQUE ,
    `mime_type` VARCHAR(150) ,
    `size` BIGINT , 
    `created_at` BIGINT NOT NULL ,
    `modified_at` BIGINT NOT NULL ,
    PRIMARY KEY (`ID`)
) ENGINE = InnoDB;