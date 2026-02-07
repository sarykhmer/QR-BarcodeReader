<?php
if (isset($_POST['date']) && isset($_POST['type'])) {
    require 'db.php';
    $date = $_POST['date'];
    $typeID = $_POST['type'];

    $stmt = $pdo->prepare("INSERT INTO tblrecord (typeID, date) VALUES (?, ?)");
    $stmt->execute([$typeID, $date]);
    $recordID = $pdo->lastInsertId();

    session_start();
    $_SESSION['recordID'] = $recordID;

    header("Location: codeReader.php");
    exit();
} else {
    header("Location: createRecord_form.php");
    exit();
}
