<?php
session_start();
$_SESSION['userID'] = 1;
header("Location: createRecord_form.php");
exit();
