<?php

if (!defined('NO_DIRECT_ACCESS')) { 
    header('HTTP/1.1 403 Forbidden');
    die(); 
}

define('DB_HOST', 'mariadb');
define('DB_USER', 'vector_usr');
define('DB_PASSWORD', 'vector_pwd');
define('DB_NAME', 'vector_db');
define('DEFAULT_TIMEZONE', 'Europe/Rome');