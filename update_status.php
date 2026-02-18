<?php
require 'checkRole.php';

header('Content-Type: application/json');

$status = $_POST['status'] ?? '';
$recordID = isset($_POST['recordID']) ? (int)$_POST['recordID'] : 0;
$allowedStatus = ['enable', 'disable'];

try {
    $stmt = $pdo->prepare("UPDATE tblRecord SET status = ? WHERE recordID = ?");
    $stmt->execute([$status, $recordID]);
    echo json_encode(['success' => true]);
    exit();
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Update failed.']);
    exit();
}
