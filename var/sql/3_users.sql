CREATE TABLE IF NOT EXISTS `users` (
    `ID` INT NOT NULL AUTO_INCREMENT , 
    `email` VARCHAR(45) NOT NULL UNIQUE, 
    `password` VARCHAR(64) NOT NULL , 
    `username` VARCHAR(25) , 
    `firstname` VARCHAR(25) , 
    `lastname` VARCHAR(25) , 
    `last_login` BIGINT DEFAULT NULL , 
    `created_at` BIGINT NOT NULL , 
    `modified_at` BIGINT NOT NULL , 
    PRIMARY KEY (`ID`)
) ENGINE = InnoDB;