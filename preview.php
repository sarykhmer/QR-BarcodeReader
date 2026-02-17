<?php
require 'checkRole.php';
if (isset($_GET['recordID'])) {
    $recordID = $_GET['recordID'];
    $stmt = $pdo->prepare("SELECT r.date, t.typeID FROM tblRecord r JOIN tblType t ON r.typeID = t.typeID WHERE r.recordID = ?");
    $stmt->execute([$recordID]);
    $record = $stmt->fetch();
    $date = $record['date'];
    $typeID = $record['typeID'];
} else {
    echo 'Data not found!';
    exit();
}

$stmt_count = $pdo->prepare("SELECT COUNT(detailID) AS totalRow FROM tblDetail WHERE recordID = ?");
$stmt_count->execute([$recordID]);
$count = $stmt_count->fetch(pdo::FETCH_ASSOC);
$row = $count['totalRow'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=deviceWidth, initial-scale=1.0">
    <title>Print Document</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <style>
        .footer {
            font-weight: bold;
            font-size: 12px;
        }

        .tableDiv {
            width: 210mm;
            height: 297mm;
            background-color: #7d7d7d;
        }

        #A4Doc {
            width: 210mm;
            height: 297mm;
            margin: auto;
            /* border: solid 1px;
            border-color: black; */
        }
    </style>
</head>

<body>
    <?php
    $stmt_retrive = $pdo->prepare("SELECT * FROM tblDetail WHERE recordID=?");
    $stmt_retrive->execute([$recordID]);
    $details = $stmt_retrive->fetchAll(PDO::FETCH_ASSOC);
    $tables = array_chunk($details, 45);
    if (empty($tables)) {
        $tables = [[]];
    }
    $count = 1;
    ?>

    <?php foreach ($tables as $tableRows): ?>
        <div id="A4Doc">
            <div class="row">
                <div class="col">
                    <img src="ocs-logo.jpeg" alt="ocs_logo" width="80px">
                </div>
                <div class="col text-center">
                    <h3 style="margin-top: 20px">OOG LOG</h3>
                </div>
                <div class="col"></div>
            </div>
            <div class="row">
                <div class="col">
                    <p style="font-size: 14px;">Date: <?= htmlspecialchars($date) ?></p>
                </div>
                <div class="col text-end">
                    <label for="departure" class="form-check-label">Departure</label>
                    <input class="form-check-input me-5" type="checkbox" id="departure" <?= ($typeID == 1) ? 'checked' : '' ?>>
                    <label for="arrival" class="form-check-label">Arrival</label>
                    <input class="form-check-input me-5" type="checkbox" id="arrival" <?= ($typeID == 2) ? 'checked' : '' ?>>
                </div>
            </div>
            <table class="text-center table-bordered" style="width: 100%; font-size: 12px;">
                <thead style="background-color: #7d7d7d">
                    <th>No.</th>
                    <th>Airline</th>
                    <th>Flight Number</th>
                    <th>Number</th>
                    <th>Unit/Item</th>
                    <th>Recive Time</th>
                    <th>Deliver Time</th>
                    <th>Total</th>
                    <th>Remark</th>
                </thead>
                <tbody>
                    <?php foreach ($tableRows as $detail): ?>
                        <tr>
                            <td><?= $count++ ?></td>
                            <td><?= $detail['airline'] ?></td>
                            <td><?= $detail['fNumber'] ?></td>
                            <td><?= $detail['sNumber'] ?></td>
                            <td><?= $detail['unit'] ?></td>
                            <td><?= date("H:i", strtotime($detail['rTime'])) ?></td>
                            <td><?= ($detail['dTime'] == "" ? "-" : date("H:i", strtotime($detail['dTime']))) ?></td>
                            <td><?= $detail['total'] ?></td>
                            <td><?= $detail['remark'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <tr style="height: 10px; background-color: #7d7d7d;">
                        <td colspan="9"></td>
                    </tr>
                </tbody>
            </table>
            <div style="width: 100%;">
                <div style="width: 50%; float: right;">
                    <p class="footer ms-5">Prepared by</p>
                </div>
                <div style="width: 50%;">
                    <p class="footer ms-5">Veryfied By</p>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
    <div class="row d-print-none">
        <div class="col text-end">
            <button class="btn btn-outline-secondary" onclick="window.history.back()">Close</button>
            <button class="btn btn-outline-primary" onclick="printWindow()">Print</button>
        </div>
    </div>
    <script>
        function printWindow() {
            window.print();
        }
    </script>
</body>

</html>