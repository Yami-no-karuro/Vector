CREATE TABLE IF NOT EXISTS `vct_assets` (
    `ID` INT NOT NULL AUTO_INCREMENT ,
    `path` VARCHAR(145) NOT NULL UNIQUE ,
    `mime_type` VARCHAR(85) ,
    `size` BIGINT , 
    `created_at` BIGINT NOT NULL ,
    `modified_at` BIGINT NOT NULL ,
    PRIMARY KEY (`ID`)
) ENGINE = InnoDB;