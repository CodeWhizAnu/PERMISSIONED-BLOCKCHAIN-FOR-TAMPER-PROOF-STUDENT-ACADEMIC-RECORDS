<?php
// includes/db.php
$host = 'localhost';
$dbname = 'academic_chain';
$username = 'root'; // Default XAMPP username
$password = '';     // Default XAMPP password is empty

try {
    // We use PDO for secure, prepared SQL statements
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    
    // Set PDO to throw exceptions on errors
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database Connection Failed: " . $e->getMessage());
}
?>