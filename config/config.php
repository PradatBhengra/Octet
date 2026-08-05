<?php
/*
 * Central configuration for the IP Management System.
 * On XAMPP the defaults below work out of the box.
 * On a live server, set real values through environment
 * variables or by editing this file (and keep it out of git).
 */

define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_PORT', (int)(getenv('DB_PORT') ?: 3306));
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');
define('DB_NAME', getenv('DB_NAME') ?: 'ip_management');
