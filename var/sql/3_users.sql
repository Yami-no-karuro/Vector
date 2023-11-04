CREATE TABLE IF NOT EXISTS `users` (
    `ID` INT NOT NULL AUTO_INCREMENT , 
    `email` VARCHAR(45) NOT NULL , 
    `password` VARCHAR(64) NOT NULL , 
    `username` VARCHAR(25) , 
    `firstname` VARCHAR(25) ,
    `lastname` VARCHAR(25) ,
    PRIMARY KEY (`ID`, `email`)
) ENGINE = InnoDB;