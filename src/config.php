<?php

if (!defined('NO_DIRECT_ACCESS')) { 
    header('HTTP/1.0 403 Forbidden');
    echo '403 Forbidden';
    die(); 
}

define('DB_HOST', '');
define('DB_USER', '');
define('DB_PASSWORD', '');
define('DB_NAME', '');
define('CIPHERING', '');        // Es.. AES-128-CTR 
define('ENCRYPTION_IV', '');    // Es.. 1234567891011121
define('ENCRYPTION_KEY', '');   // Es.. fZBp$1n3o^Uu3!Y3*19K 
define('DEFAULT_TIMEZONE', 'Europe/Rome');
define('BASE_URL', 'http://localhost:8000/');
define('PUBLIC_URL', 'http://localhost:8000/public/');
define('API_URL', 'http://localhost:8000/api/');