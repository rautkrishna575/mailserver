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
define('SMTP_HOST', 'smtp.gmail.com');       // Your SMTP server, e.g., smtp.gmail.com
define('SMTP_USERNAME', 'prateekraut575@gmail.com'); // Your SMTP username
define('SMTP_PASSWORD', 'iwlw bqlj vfpc sjjc');       // Your SMTP password or App Password for Gmail
define('SMTP_PORT', 465);                        // 587 for TLS, 465 for SSL
define('SMTP_SECURE', 'ssl');                    // 'tls' or 'ssl'
define('MAIL_FROM_ADDRESS', 'prateekraut575@gmail.com');
define('MAIL_FROM_NAME', 'mailserver');
// Application URL (NO trailing slash)
define('APP_URL', 'http://localhost/mailserver');
