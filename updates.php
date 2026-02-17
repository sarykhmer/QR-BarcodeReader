<?php
require 'checkRole.php';

header('Content-Type: application/json');

$action = $_REQUEST['action'] ?? 'fetch';
$recordID = isset($_GET['recordID']) ? (int)$_GET['recordID'] : 0;
$allowedFields = ['airline', 'fNumber', 'sNumber', 'unit', 'rTime', 'dTime', 'total', 'remark'];
if ($action === 'update') {
    $field = $_POST['field'] ?? '';
    $value = $_POST['value'] ?? '';
    $detailID = isset($_POST['detailID']) ? (int)$_POST['detailID'] : 0;

    if (!in_array($field, $allowedFields, true) || $detailID <= 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid input.']);
        exit();
    }

    try {
        $stmt = $pdo->prepare("UPDATE tblDetail SET $field = ? WHERE detailID = ?");
        $stmt->execute([$value, $detailID]);
        echo json_encode(['success' => true]);
        exit();
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Update failed.']);
        exit();
    }
}

if ($recordID <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing recordID.']);
    exit();
}

$keyWord = "%" . $_GET['keyWord'];
try {
    $stmt = $pdo->prepare("SELECT detailID, airline, fNumber, sNumber, unit, rTime, dTime, total, remark FROM tblDetail WHERE recordID = ? AND sNumber LIKE '" . $keyWord . "' ORDER BY detailID DESC");
    $stmt->execute([$recordID]);
    $details = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['success' => true, 'details' => $details]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Search failed.']);
}
