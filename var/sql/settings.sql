CREATE TABLE IF NOT EXISTS `settings` (
    `key` VARCHAR(50) NOT NULL ,
    `value` VARCHAR(255) NOT NULL ,
    PRIMARY KEY (`key`)
) ENGINE = InnoDB;