# Vector
## A simple HttpFoundation framework for PHP.  

### Installation (Default)
Download and extract the source code.  
Run `` composer install ``.  

### Installation (Docker)
Download and extract the source code.  
Run `` echo `id -u`:`id -g` `` to retrieve your local user and group id.  
If the echo result is different from `` 1000:1000 `` open the `` Dockerfile `` and override `` usermod `` and `` groupmod `` values.  
Run `` docker-compose up -d `` to initialize the container.  
Once the container is up run `` docker compose exec php composer install ``.  

### Notes
If you run into permissions problems be sure that the `` src/var `` is owned by `` www-data ``.  
If you are on Docker run `` docker compose exec php chown -R www-data ./src/var/ ``.  

### Encore Setup
Run `` npm install `` to install node dependencies.  
Run `` npm run watch ``  to start the watcher on the `` assets `` folder.  
By default webpack compiles inside `` public/assets/build `` but you can customize the defaul configuration in `` webpack.config.js `` located in the project root.  
See `` package.json `` to explore encore available scripts.  

# Customization and Settings

## Transients
By default Vector store transients data as md5 named files inside `` src/var/cache/transients ``.  
You can change the default behaviour (usually not recommended) and save transients on the database by setting `` define('DATABASE_TRANSIENTS', true); `` in `` config.php ``.  
Be sure to create the transients table.
```
CREATE TABLE `vector_db`.`transients` (
    `ID` INT NOT NULL AUTO_INCREMENT , 
    `name` VARCHAR(50) NOT NULL , 
    `data` TEXT NOT NULL , 
    `time` INT(11) NOT NULL , 
    PRIMARY KEY (`ID`)
) ENGINE = InnoDB; 
```

## Logs
By default Vector store logs data in a log_type.log.txt file inside `` src/var/logs ``.  
You can change the default behaviour (usually not recommended) and save logs on the database by setting `` define('DATABASE_LOGS', true); `` in `` config.php ``.  
Be sure to create the logs table.  
```
CREATE TABLE `vector_db`.`logs` (
    `ID` INT NOT NULL AUTO_INCREMENT , 
    `type` VARCHAR(50) NOT NULL , 
    `content` TEXT NOT NULL , 
    PRIMARY KEY (`ID`)
) ENGINE = InnoDB; 
```

## Routes
By default Vector store route data as md5 named files inside `` src/var/cache/routes ``.  
You can change the default behaviour (usually not recommended) and save route data on the database by setting `` define('DATABASE_ROUTES', true); `` in `` config.php ``.  
Be sure to create the logs table.  
```
CREATE TABLE `vector_db`.`routes` (
    `ID` INT NOT NULL AUTO_INCREMENT ,
    `path` VARCHAR(125) NOT NULL ,
    `regex` VARCHAR(185) NOT NULL ,
    `methods` TEXT NOT NULL ,
    `controller` VARCHAR(85) NOT NULL ,
    `callback` VARCHAR(50) NOT NULL ,
    PRIMARY KEY (`ID`, `path`)
) ENGINE = InnoDB;
```