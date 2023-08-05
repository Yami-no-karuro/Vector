CREATE TABLE IF NOT EXISTS `transients` (
    `ID` INT NOT NULL AUTO_INCREMENT , 
    `name` VARCHAR(50) NOT NULL , 
    `content` TEXT NOT NULL ,
    PRIMARY KEY (`ID`, `name`)
) ENGINE = InnoDB;