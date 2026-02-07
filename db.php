<?php
session_start();
$host = 'localhost';
$db = 'dbOOGRecord';
$user = 'root'; // Your database username
$pass = 'root'; // Your database password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
