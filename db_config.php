<?php
// Database configuration
$host = 'localhost';
$dbname = 'CityExplorerDB'; // your database name
$username = 'root';         // default XAMPP user
$password = '';             // default XAMPP password is empty

$dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";

try {
    // Create PDO instance
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    // If connection fails, return JSON error and stop
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'error' => 'Database connection failed'
    ]);
    exit;
}
