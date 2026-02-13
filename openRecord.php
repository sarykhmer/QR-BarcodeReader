<?php
require 'checkRole.php';
if (isset($_GET['recordID'])) {
    $_SESSION['recordID'] = $_GET['recordID'];
}
if (isset($_GET['field']) && isset($_GET['value']) && isset($_GET['detailID'])) {
    $field = $_GET['field'];
    $value = $_GET['value'];
    $detailID = $_GET['detailID'];
    $stmt_update = $pdo->prepare("UPDATE tblDetail SET $field=? WHERE detailID=?");
    $stmt_update->execute([$value, $detailID]);
}
if (!isset($_SESSION['recordID'])) {
    header("Location: createRecord_form.php");
}
$recordID = $_SESSION['recordID'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Report</title>
    <link rel="stylesheet" href="./css/bootstrap.min.css">
    <style>
    </style>
</head>

<body>
    <div class="container" style="border: 1px; border-color: green">
        <div class="row">
            <div class="col">
                <img src="ocs-logo.jpeg" alt="ocs_logo" width="100px">
            </div>
        </div>
        <div class="row">
            <div class="col text-center">
                <h2 style="margin-top: -70px;">OOG LOOG</h2>
            </div>
        </div>

        <div class="row">
            <div class="col">
                <p>Date: 13-Feb-2026</p>
            </div>
            <div class="col text-end">
                <input class="form-check-input" type="checkbox" id="departure" checked readonly>
                <label for="departure" class="form-check-label">Departure</label>
                <input class="form-check-input" type="checkbox" id="arrival" readonly>
                <label for="departure" class="form-check-label">Arrival</label>
            </div>
        </div>

        <div class="row">
            <div class="col">
                <table class="table text-center" style="width: 100vw;">
                    <thead>
                        <th>No</th>
                        <th>Airline</th>
                        <th>Flight Number</th>
                        <th>Serial Number</th>
                        <th>Unit/Item</th>
                        <th>Recive Time</th>
                        <th>Deliver Time</th>
                        <th>Total</th>
                        <th>Remark</th>
                    </thead>
                    <tbody>
                        <?php
                        $stmt_retrive = $pdo->prepare("SELECT * FROM tblDetail WHERE recordID=?");
                        $stmt_retrive->execute([$recordID]);
                        $details = $stmt_retrive->fetchAll();
                        $count = 1;
                        foreach ($details as $detail) {
                            echo "<tr>";
                            echo "<td>" . $count++ . "</td>";
                            echo "<td> <input type='text' value='" . $detail['airline'] . "' onchange='updateField(\"airline\", " . $detail['detailID'] . ")'></td>";
                            echo "<td> <input type='text' value='" . $detail['fNumber'] . "' onchange='updateField(\"fNumber\", " . $detail['detailID'] . ")'></td>";
                            echo "<td> <input type='text' value='" . $detail['sNumber'] . "' onchange='updateField(\"sNumber\", " . $detail['detailID'] . ")'></td>";
                            echo "<td> <input type='text' value='" . $detail['unit'] . "' onchange='updateField(\"unit\", " . $detail['detailID'] . ")'></td>";
                            echo "<td> <input type='text' value='" . $detail['rTime'] . "' onchange='updateField(\"rTime\", " . $detail['detailID'] . ")'></td>";
                            echo "<td> <input type='text' value='" . $detail['dTime'] . "' onchange='updateField(\"dTime\", " . $detail['detailID'] . ")'></td>";
                            echo "<td> <input type='text' value='" . $detail['total'] . "' onchange='updateField(\"total\", " . $detail['detailID'] . ")'></td>";
                            echo "<td> <input type='text' value='" . $detail['remark'] . "' onchange='updateField(\"remark\", " . $detail['detailID'] . ")'></td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="footer"></div>
    </div>

    <script>
        function updateField(field, detailID) {
            const value = event.target.value;
            window.location.href = `openRecord.php?field=${field}&value=${value}&detailID=${detailID}`;
        }
    </script>
</body>

</html>