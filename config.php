<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'funko_webshop');
define('DB_USER', 'root');
define('DB_PASS', 'root'); 
define('DB_CHARSET', 'utf8');

function getDatabaseConnection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($conn->connect_error) {
        die("Database connectie fout: " . $conn->connect_error);
    }
    
    $conn->set_charset(DB_CHARSET);
    return $conn;
}

$mysqli = getDatabaseConnection();
?>