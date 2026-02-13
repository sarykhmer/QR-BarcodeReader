<?php
require 'db.php';
if (isset($_GET['recordID'])) {
    $recordID = $_GET['recordID'] ?? null;
    $stmt = $pdo->prepare("SELECT tblRecord.date,  tblType.type FROM tblRecord JOIN tblType ON tblRecord.typeID = tblType.typeID WHERE recordID = ?");
    $stmt->execute([$recordID]);
    $recordInfo = $stmt->fetch(PDO::FETCH_ASSOC);
    $recordName = $recordInfo['date'] . ' - ' . $recordInfo['type'];
    session_start();
    $_SESSION['recordID'] = $recordID;
    $_SESSION['recordName'] = $recordName;
    header("Location: codeReader.php");
    exit();
} else {
    header("location: login.php");
    exit();
}
