<?php
require 'checkRole.php';
if (isset($_GET['recordID']) && isset($_GET['date']) && isset($_GET['typeID'])) {
    $_SESSION['recordID'] = $_GET['recordID'];
    $_SESSION['date'] = $_GET['date'];
    $_SESSION['typeID'] = $_GET['typeID'];
}
if (!isset($_SESSION['recordID'])) {
    header("Location: createRecord_form.php");
    exit();
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
        input {
            width: 80%;
        }
    </style>
</head>

<body>
    <div class="container" style="border: 1px; border-color: green">
        <div class="row">
            <div class="col text-end">
                <button class="btn btn-sm btn-warning" onclick="fetchDetails()">Update <i class="bi bi-arrow-repeat" style="font-size: large;"></i></button>
                <button class="btn btn-sm btn-success" onclick="share(<?= $recordID ?>)"><i class="bi bi-share-fill"></i></button>
                <button class="btn btn-sm btn-primary" onclick="openPrint(<?= $recordID ?>)"><i class="bi bi-printer-fill"></i></button>
                <a href="dashboard.php">
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
                <h2 style="margin-top: -70px;">OOG LOG</h2>
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
                        $stmt_retrive = $pdo->prepare("SELECT detailID, airline, fNumber, sNumber, unit, rTime, dTime, total, remark FROM tblDetail WHERE recordID=? ORDER BY detailID DESC");
                        $stmt_retrive->execute([$recordID]);
                        $details = $stmt_retrive->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($details as $detail) {
                        ?>
                            <tr>
                                <td></td>
                                <td><input type="text" value="<?= htmlspecialchars($detail['airline']) ?>" onchange="updateField('airline', <?= (int)$detail['detailID'] ?>, this.value)"></td>
                                <td><input type="text" value="<?= htmlspecialchars($detail['fNumber']) ?>" onchange="updateField('fNumber', <?= (int)$detail['detailID'] ?>, this.value)"></td>
                                <td><input type="text" value="<?= htmlspecialchars($detail['sNumber']) ?>" onchange="updateField('sNumber', <?= (int)$detail['detailID'] ?>, this.value)"></td>
                                <td><input type="text" value="<?= htmlspecialchars($detail['unit']) ?>" onchange="updateField('unit', <?= (int)$detail['detailID'] ?>, this.value)"></td>
                                <td><input type="text" value="<?= htmlspecialchars($detail['rTime']) ?>" onchange="updateField('rTime', <?= (int)$detail['detailID'] ?>, this.value)"></td>
                                <td><input type="text" value="<?= htmlspecialchars($detail['dTime']) ?>" onchange="updateField('dTime', <?= (int)$detail['detailID'] ?>, this.value)"></td>
                                <td><input type="text" value="<?= htmlspecialchars($detail['total']) ?>" onchange="updateField('total', <?= (int)$detail['detailID'] ?>, this.value)"></td>
                                <td><input type="text" value="<?= htmlspecialchars($detail['remark']) ?>" onchange="updateField('remark', <?= (int)$detail['detailID'] ?>, this.value)"></td>
                            </tr>
                        <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="footer"></div>
    </div>

    <script>
        const recordID = <?= (int)$recordID ?>;
        const detailsBody = document.querySelector("tbody");
        let isEditing = false;

        detailsBody.addEventListener("focusin", () => {
            isEditing = true;
        });
        detailsBody.addEventListener("focusout", () => {
            isEditing = false;
        });

        function escapeHtml(value) {
            const div = document.createElement("div");
            div.textContent = value ?? "";
            return div.innerHTML;
        }

        function renderDetails(details) {
            let html = "";
            details.forEach((detail, index) => {
                const rowNo = details.length - index;
                html += `
                    <tr>
                        <td>${rowNo}</td>
                        <td><input type="text" value="${escapeHtml(detail.airline ?? "")}" onchange="updateField('airline', ${detail.detailID}, this.value)"></td>
                        <td><input type="text" value="${escapeHtml(detail.fNumber ?? "")}" onchange="updateField('fNumber', ${detail.detailID}, this.value)"></td>
                        <td> <i class="bi bi-eye-fill" onclick='viewBarcode(${JSON.stringify(detail.sNumber ?? "")})'> <input type="text" value="${escapeHtml(detail.sNumber ?? "")}" onchange="updateField('sNumber', ${detail.detailID}, this.value)"></></td>
                        <td><input type="text" value="${escapeHtml(detail.unit ?? "")}" onchange="updateField('unit', ${detail.detailID}, this.value)"></td>
                        <td><input type="text" value="${escapeHtml(detail.rTime ?? "")}" onchange="updateField('rTime', ${detail.detailID}, this.value)"></td>
                        <td><input type="text" value="${escapeHtml(detail.dTime ?? "")}" onchange="updateField('dTime', ${detail.detailID}, this.value)"></td>
                        <td><input type="text" value="${escapeHtml(detail.total ?? "")}" onchange="updateField('total', ${detail.detailID}, this.value)"></td>
                        <td><input type="text" value="${escapeHtml(detail.remark ?? "")}" onchange="updateField('remark', ${detail.detailID}, this.value)"></td>
                    </tr>
                `;
            });
            detailsBody.innerHTML = html;
        }

        async function fetchDetails() {
            if (isEditing) return;
            try {
                const response = await fetch(`updates.php?action=fetch&recordID=${recordID}`);
                const data = await response.json();
                if (!data.success || !Array.isArray(data.details)) {
                    return;
                }
                renderDetails(data.details);
            } catch (error) {
                console.error("Polling failed:", error);
            }
        }

        async function updateField(field, detailID, value) {
            try {
                const body = new URLSearchParams({
                    action: "update",
                    field,
                    value,
                    detailID: String(detailID),
                });

                const response = await fetch("updates.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded",
                    },
                    body: body.toString(),
                });
                const data = await response.json();
                if (!data.success) {
                    alert("Update failed.");
                }
            } catch (error) {
                alert("Update failed.");
            }
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

        function openPrint(recordID) {
            window.location.href = `./preview.php?recordID=${recordID}`;
        }

        fetchDetails();
        setInterval(fetchDetails, 1500);

        function viewBarcode(sNumber) {
            window.open(`createBarcode.html?value=${encodeURIComponent(sNumber)}`, '_blank', 'width=600,height=400');
        }
    </script>
</body>

</html>