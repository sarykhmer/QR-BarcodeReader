<?php
require 'checkRole.php';
if (isset($_GET['recordID']) && isset($_GET['date']) && isset($_GET['typeID'])) {
    $_SESSION['recordID'] = $_GET['recordID'];
    $_SESSION['date'] = $_GET['date'];
    $_SESSION['typeID'] = $_GET['typeID'];
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
$date = $_SESSION['date'];
$typeID = $_SESSION['typeID'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Report</title>
    <link rel="stylesheet" href="./css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <style>
    </style>
</head>

<body>
    <div class="container" style="border: 1px; border-color: green">
        <div class="row">
            <div class="col text-end">
                <button class="btn btn-sm btn-warning" onclick="window.location.reload()">Update <i class="bi bi-arrow-repeat" style="font-size: large;"></i></button>
                <button class="btn btn-sm btn-success" onclick="share(<?= $recordID ?>)"><i class="bi bi-share-fill"></i></button>
                <button class="btn btn-sm btn-primary"><i class="bi bi-printer-fill"></i></button>
                <a href="createRecord_form.php">
                    <button class="btn btn-danger btn-sm">Exit <i class="bi bi-box-arrow-right"></i>
                    </button>
                </a>
            </div>
        </div>
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
                <p>Date: <?= htmlspecialchars($date) ?></p>
            </div>
            <div class="col text-end">
                <input class="form-check-input" type="checkbox" id="departure" <?= ($typeID == 1) ? 'checked' : '' ?> disabled>
                <label for="departure" class="form-check-label">Departure</label>
                <input class="form-check-input ms-3" type="checkbox" id="arrival" <?= ($typeID == 2) ? 'checked' : '' ?> disabled>
                <label for="arrival" class="form-check-label">Arrival</label>
            </div>
        </div>

        <div class="row">
            <div class="col">
                <table class="table text-center">
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

        function share(recordID) {
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "generateToken.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.send(`recordID=${recordID}`);
            xhr.onreadystatechange = function() {
                if (xhr.readyState === XMLHttpRequest.DONE) {
                    if (xhr.status === 200) {
                        const response = JSON.parse(xhr.responseText);
                        const token = response.token;
                        const shareURL = `${window.location.origin}/qr-barcodereader/gateway.php?token=${token}`;
                        // Copy to clipboard automatically
                        navigator.clipboard.writeText(shareURL).then(function() {
                            alert("Link copied to clipboard!");
                        }).catch(function(err) {
                            // Fallback if clipboard API fails
                            prompt("Share this URL:", shareURL);
                        });
                    } else {
                        alert("Error generating token. Please try again.");
                    }
                }
            }
        }
    </script>
</body>

</html>