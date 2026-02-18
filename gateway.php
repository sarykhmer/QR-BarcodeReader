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
    $_SESSION['recordID'] = $recordID;
    $_SESSION['recordName'] = $recordName;
    echo "<script> window.open('codeReader.php','_blank')</script>";
} else {
    header("location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gateway</title>
    <style>
        button {
            width: 100px;
            height: 100px;
            border-radius: 50px;
            font-size: 16px;
            color: black;
            position: fixed;
            bottom: 50%;
            left: 50%;
            margin: auto;
            background-color: greenyellow;
        }
    </style>
</head>

<body>
    <div>
        <button onclick="window.location.reload()">Re-Open Page</button>
    </div>
</body>

</html>