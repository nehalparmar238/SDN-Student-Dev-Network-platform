<?php

define('BASE_URL', 'http://localhost/SDN');
define('DB_HOST', 'localhost');
define('DB_NAME', 'sdn_db');
define('DB_USER', 'root');
define('DB_PASS', '');
define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH', ROOT_PATH . '/app');

date_default_timezone_set('Asia/Kolkata');

ini_set('display_errors', '1');
error_reporting(E_ALL);