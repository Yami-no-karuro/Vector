<?php

if (!defined('NO_DIRECT_ACCESS')) { 
    header('HTTP/1.1 403 Forbidden');
    die(); 
}

const DB_HOST = 'mariadb';
const DB_USER = 'vector_usr';
const DB_PASSWORD = 'vector_pwd';
const DB_NAME = 'vector_db';

const DEFAULT_TIMEZONE = 'Europe/Rome';
const DATABASE_TRANSIENTS = false;
const DATABASE_LOGS = false;