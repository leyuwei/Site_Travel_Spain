<?php
// Database connection settings
$host = 'localhost';
$db   = 'travel_db';
$user = 'travel_user';
$pass = 'travel_pass';
$charset = 'utf8mb4';

// Simple shared password for login
$login_password = 'XXXXXX';
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];
try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
    exit;
}
?>
