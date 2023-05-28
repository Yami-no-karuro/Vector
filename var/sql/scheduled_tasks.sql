CREATE TABLE IF NOT EXISTS `scheduled_tasks` (
    `ID` INT NOT NULL AUTO_INCREMENT , 
    `callback` TEXT NOT NULL , 
    `params` TEXT NOT NULL , 
    `time` BIGINT NOT NULL , 
    PRIMARY KEY (`ID`)
) ENGINE = InnoDB;