CREATE TABLE IF NOT EXISTS `vct_users` (
    `ID` INT NOT NULL AUTO_INCREMENT , 
    `email` VARCHAR(85) NOT NULL UNIQUE, 
    `password` VARCHAR(64) NOT NULL , 
    `username` VARCHAR(45) , 
    `last_login` BIGINT DEFAULT NULL , 
    `created_at` BIGINT NOT NULL , 
    `modified_at` BIGINT NOT NULL , 
    PRIMARY KEY (`ID`)
) ENGINE = InnoDB;