<?php
require 'checkRole.php';
if (isset($_POST['date']) && isset($_POST['type'])) {
    require 'db.php';
    $date = $_POST['date'];
    $typeID = $_POST['type'];

    $stmt = $pdo->prepare("INSERT INTO tblrecord (typeID, date) VALUES (?, ?)");
    $success = $stmt->execute([$typeID, $date]);
    $recordID = $pdo->lastInsertId();

    if ($success) {
        $stmt = $pdo->prepare("SELECT tblRecord.date,  tblType.type FROM tblRecord JOIN tblType ON tblRecord.typeID = tblType.typeID WHERE recordID = ?");
        $stmt->execute([$recordID]);
        $recordInfo = $stmt->fetch(PDO::FETCH_ASSOC);
        $recordName = $recordInfo['date'] . ' - ' . $recordInfo['type'];
        session_start();
        $_SESSION['recordID'] = $recordID;
        $_SESSION['typeID'] = $typeID;
        $_SESSION['date'] = $date;
        $_SESSION['recordName'] = $recordName;
    }

    header("Location: openRecord.php");
    exit();
} else {
    $recordID = $_GET['recordID'] ?? null;
    $recordName = $_GET['name'] ?? null;
    session_start();
    $_SESSION['recordID'] = $recordID;
    $_SESSION['recordName'] = $recordName;
    header("Location: codeReader.php");
    exit();
}
