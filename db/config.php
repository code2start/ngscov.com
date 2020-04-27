<?php
// DB credentials.
define('DB_HOST', 'localhost');
define('DB_USER', 'ngscovuser');
define('DB_PASS', 'ngscovP@ssw0rd');
define('DB_NAME', 'ngscov');
// Establish database connection.
try {
    $dbh = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
} catch (PDOException $e) {
    exit("Error: " . $e->getMessage());
}
