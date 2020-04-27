<?php
// DB credentials.
define('DB_HOST', 'mysql');
define('DB_USER', 'root');
define('DB_PASS', 'root');
define('DB_NAME', 'ngscov');
// Establish database connection.
try {
    $dbh = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
} catch (PDOException $e) {
    exit("Error: " . $e->getMessage());
}
