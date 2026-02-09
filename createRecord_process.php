<?php
if (isset($_POST['date']) && isset($_POST['type'])) {
    require 'db.php';
    $date = $_POST['date'];
    $typeID = $_POST['type'];

    $stmt = $pdo->prepare("INSERT INTO tblrecord (typeID, date) VALUES (?, ?)");
    $success = $stmt->execute([$typeID, $date]);
    $recordID = $pdo->lastInsertId();

    if ($success) {
        session_start();
        $_SESSION['recordID'] = $recordID;
        $_SESSION['recordType'] = $typeID;
    }

    header("Location: codeReader.php");
    exit();
} else {
    $recordID = $_GET['recordID'] ?? null;
    $typeID = $_GET['typeID'] ?? null;
    session_start();
    $_SESSION['recordID'] = $recordID;
    $_SESSION['recordType'] = $typeID;
    header("Location: codeReader.php");
    exit();
}
