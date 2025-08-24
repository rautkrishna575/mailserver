<?php
// /config.php

// Error Reporting (for development)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'db_mailserver');
define('DB_USER', 'root');
define('DB_PASS', 'root');

// Application URL (NO trailing slash)
define('APP_URL', 'http://localhost/mailserver');
