<?php
if ($_SERVER['REQUEST_METHOD' == 'POST']) {
    $userName = $_POST['userName'];
    $password = $_POST['password'];
    $stmt = $pdo->prepare("SELECT * FROM tblUser WHERE userName = ? ");
    $stmt->execute([$userName]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['roleID'] = $user['roleID'];
        header("Location: dashboard.php");
        exit();
    }
}
