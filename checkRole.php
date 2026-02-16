<?php
require 'db.php';
if (!isset($_SESSION['roleID'])) {
    header("Location: login.php");
    exit();
}
