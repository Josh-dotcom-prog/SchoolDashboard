<?php
// Database connection settings

error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = 'localhost';
$dbname = 'school_system';
$username = 'root';
$password = '020381';

try {
    $pdo = new PDO("mysql:host=localhost;dbname=school_system", "root", "020381");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "Connected successfully to the database!";
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>