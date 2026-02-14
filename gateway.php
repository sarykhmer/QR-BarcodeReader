<?php
require 'db.php';
if (isset($_GET['token'])) {
    $token = $_GET['token'] ?? null;
    $stmt = $pdo->prepare("SELECT tblRecord.date,  tblType.type, tblRecord.recordID FROM tblRecord JOIN tblType ON tblRecord.typeID = tblType.typeID WHERE token = ?");
    $stmt->execute([$token]);
    $recordInfo = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$recordInfo) {
        echo "Record not found.";
        exit();
    }
    $recordID = $recordInfo['recordID'];
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
