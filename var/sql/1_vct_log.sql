CREATE TABLE IF NOT EXISTS `vct_logs` (
    `ID` INT NOT NULL AUTO_INCREMENT ,
    `domain` VARCHAR(50) NOT NULL ,
    `time` BIGINT NOT NULL ,
    `log` TEXT NOT NULL ,
    FULLTEXT (`log`) ,
    PRIMARY KEY (`ID`)
) ENGINE = InnoDB;