CREATE TABLE IF NOT EXISTS `transients` (
    `ID` INT NOT NULL AUTO_INCREMENT , 
    `name` VARCHAR(50) NOT NULL , 
    `data` TEXT NOT NULL , 
    `time` BIGINT NOT NULL , 
    PRIMARY KEY (`ID`)
) ENGINE = InnoDB;