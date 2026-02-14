<?php
require 'db.php';
if (isset($_POST['recordID'])) {
    $recordID = $_POST['recordID'];
    $token = bin2hex(random_bytes(16));
    $stmt_insert = $pdo->prepare("UPDATE tblRecord SET token=? WHERE recordID=?");
    $stmt_insert->execute([$token, $recordID]);
    echo json_encode(['token' => $token]);
}
