<?php
require_once 'db.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $userName = $_POST['userName'];
    $password = $_POST['password'];
    $stmt = $pdo->prepare("SELECT * FROM tblUser WHERE userName = ? ");
    $stmt->execute([$userName]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['userID'] = $user['userID'];
        header("Location: dashboard.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log In</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <style>
        body {
            background: #0051ff;
            background: linear-gradient(90deg, rgba(0, 81, 255, 1) 0%, rgba(15, 6, 56, 1) 47%, rgba(155, 8, 168, 1) 100%);
        }

        .form-control {
            background-color: transparent;
            color: white;
        }

        .container {
            width: 400px;
            height: 50vh;
            margin: auto;
            margin-top: 100px;
            background-color: transparent;
            color: white;
            border: 1px solid white;
            border-radius: 30px;
        }
    </style>
</head>

<body>
    <div class="container text-center">
        <div class="row mb-5">
            <div class="col">
                <h1>Log In</h1>
            </div>
        </div>
        <form action="login.php" method="post">
            <div class="row mb-3">
                <div class="col col-3">
                    <label for="userName" class="form-label">username: </label>
                </div>
                <div class="col col-9">
                    <input type="text" id="userName" class="form-control" name="userName" required>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col col-3">
                    <label for="password" class="form-input-label">Password: </label>
                </div>
                <div class="col col-9">
                    <input type="password" id="password" class="form-control" name="password" required>
                </div>
            </div>
            <div class="row mt-5">
                <div class="col">
                    <button class="btn btn-outline-success" type="submit">Login</button>
                </div>
            </div>
        </form>
    </div>

</body>

</html>