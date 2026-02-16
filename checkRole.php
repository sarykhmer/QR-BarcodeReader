<?php
require 'db.php';
if (!isset($_SESSION['userID'])) {
    header("Location: login.php");
    exit();
}
$userID = $_SESSION['userID'];
$stmt = $pdo->prepare("SELECT * FROM tblUser WHERE userID = ?");
$stmt->execute([$userID]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
$roleID = $user['roleID'];
$typeID = $user['typeID'];
