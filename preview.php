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
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=210mn, initial-scale=1.0">
    <title>Print Document</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <style>
        .footer {
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="container" id="print">
        <div class="row">
            <div class="col">
                <img src="ocs-logo.jpeg" alt="ocs_logo" width="100px">
            </div>
        </div>
        <div class="row">
            <div class="col text-center">
                <h2 style="margin-top: -70px;">OOG LOG</h2>
            </div>
        </div>

        <div class="row">
            <div class="col">
                <p>Date: <?= htmlspecialchars($date) ?></p>
            </div>
            <div class="col text-end">
                <label for="departure" class="form-check-label">Departure</label>
                <input class="form-check-input me-5" type="checkbox" id="departure" <?= ($typeID == 1) ? 'checked' : '' ?>>
                <label for="arrival" class="form-check-label">Arrival</label>
                <input class="form-check-input me-5" type="checkbox" id="arrival" <?= ($typeID == 2) ? 'checked' : '' ?>>
            </div>
        </div>
        <div class="row" id="divTable">
            <div class="col-12">
                <table class="text-center table-bordered" style="width: 100%; font-size: 14px;">
                    <thead style="background-color: #7d7d7d">
                        <th>No.</th>
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
                            echo "<td>" . $detail['airline'] . "</td>";
                            echo "<td>" . $detail['fNumber'] . "</td>";
                            echo "<td>" . $detail['sNumber'] . "</td>";
                            echo "<td>" . $detail['unit'] . "</td>";
                            echo "<td>" . $detail['rTime'] . "</td>";
                            echo "<td>" . $detail['dTime'] . "</td>";
                            echo "<td>" . $detail['total'] . "</td>";
                            echo "<td>" . $detail['remark'] . "</td>";
                            echo "</tr>";
                        }
                        ?>
                        <tr style="height: 16px; background-color: #7d7d7d;">
                            <td colspan="9"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-6">
                <p class="footer ms-5">Prepared by</p>
            </div>
            <div class="col-6">
                <p class="footer ms-5">Veryfied By</p>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col text-end">
            <button class="btn btn-outline-secondary" onclick="window.history.back()">Close</button>
            <button class="btn btn-outline-primary" onclick="print()">Print</button>
        </div>
    </div>
    <script>
        function print() {
            const printSection = document.getElementById('print');
            if (!printSection) return;

            const printWindow = window.open('', '_blank');
            if (!printWindow) return;

            printWindow.document.open();
            printWindow.document.write(`
                <!DOCTYPE html>
                <html>
                <head>
                    <meta charset="UTF-8">
                    <title>Print</title>
                    <link rel="stylesheet" href="css/bootstrap.min.css">
                </head>
                <body>
                    ${printSection.outerHTML}
                </body>
                </html>
            `);
            printWindow.document.close();
            printWindow.focus();
            printWindow.print();
            printWindow.close();
        }
    </script>
</body>

</html>