<?php
require 'db.php';
if (!isset($_SESSION['recordID'])) {
    header("Location: login.php");
    exit();
}

$recordID = $_SESSION['recordID'];
$recordName = $_SESSION['recordName'] ?? 'Unknown Record';
if (isset($_GET['code']) && isset($_GET['timeScan'])) {

    $code = trim($_GET['code']);
    $timeScan = $_GET['timeScan'];
    $unit = $_GET['unit'] ?? '0';

    // restrive code in database
    $stmt_retrive = $pdo->prepare("SELECT * FROM tblDetail WHERE recordID = ? AND sNumber = ?");
    $stmt_retrive->execute([$recordID, $code]);
    $exist = $stmt_retrive->fetch(PDO::FETCH_ASSOC);
    // if code don't exist, insert new record
    if (!$exist) {
        try {
            $stmt_insert = $pdo->prepare("INSERT INTO tblDetail (recordID, sNumber, rTime, unit) VALUES (?, ?, ?, ?)");
            $stmt_insert->execute([$recordID, $code, $timeScan, $unit]);
        } catch (PDOException $e) {
            // Handle the error, e.g., log it and show a user-friendly message
            error_log("Database error: " . $e->getMessage());
            die("An error occurred while inserting the record! <br> " . $e);
        }
    } elseif ($exist) {
        // if code exist, update dTime
        try {
            $stmt_update = $pdo->prepare("UPDATE tblDetail SET dTime = ? WHERE recordID = ? AND sNumber = ?");
            $stmt_update->execute([$timeScan, $recordID, $code]);
        } catch (PDOException $e) {
            // Handle the error, e.g., log it and show a user-friendly message
            error_log("Database error: " . $e->getMessage());
            die("An error occurred while updating the record! <br> " . $e);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Barcode & QR Scanner</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- html5-qrcode library -->
    <script src="js/html5-qrcode.min.js"></script>

    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
        }

        .container {
            width: 100vw;
            height: 90vh;
            max-width: 600px;
            margin: auto;
        }


        .view {
            width: 100%;
            height: 70%;
            position: relative;
        }

        .name {
            width: 100%;
            display: flex;
            margin-right: 10px;
            margin-left: 10px;
            justify-content: center;
        }

        #btnLogout {
            border-radius: 10px;
            color: white;
            float: right;
            background-color: red;
            position: fixed;
            right: 30px;
            top: 10px;
        }

        .input {
            width: 100%;
            margin-right: 10px;
            margin-left: 10px;
            position: absolute;
            bottom: 0;
        }

        #unit {
            width: 20%;
            font-size: 18px;
            float: left;
        }

        #inputCode {
            width: 70%;
            border-radius: 20px;
            font-size: 18px;
            text-align: center;
            float: left;
        }

        #reader {
            width: 100%;
            z-index: 999;
        }

        #result {
            z-index: 1000;
            color: green;
            position: absolute;
        }

        #time {
            z-index: 1000;
            color: green;
            position: absolute;
            margin-top: 60px;
        }

        #alert {
            z-index: 1000;
            margin-top: 100px;
            position: absolute;
        }

        .result {
            width: 100%;
            display: flex;
            justify-content: center;
        }

        button {
            font-size: 18px;
        }

        .controller {
            width: 100%;
            height: auto;
            display: flex;
            justify-content: center;
            background-color: green;
        }

        #btnScan {
            color: green;
            border: none;
            border-radius: 50%;
            width: 150px;
            height: 150px;
            margin-top: 10px;
            position: absolute;
            align-self: center;
            z-index: 999;
            bottom: 0;
        }

        #btnFlash {
            border-radius: 20px;
            width: 150px;
            height: 30px;
            z-index: 999;
            position: absolute;
            bottom: 160px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="view">
            <!-- Display record name and logout button -->
            <div class="name">
                <p style="text-align: right"><?= htmlspecialchars($recordName) ?></p>
                <button id="btnLogout" onclick="location.href='logout.php'">Logout</button>
            </div>

            <!-- Display scan result, alert, time -->
            <div class="result">
                <h2 id="result">📷 Barcode / QR Code Scanner</h2>
                <h2 id="time"></h2>
                <p id="alert"></p>
            </div>

            <div id="reader">
            </div>
            <div class="input">
                <input type="text" id="inputCode" oninput="this.value=this.value.replace(/[^0-9]/g,'')" placeholder="Input manual...">
                <select name="unit" id="unit">
                    <?php
                    $retrive_unit = $pdo->prepare("SELECT * FROM tblUnit");
                    $retrive_unit->execute();
                    $units = $retrive_unit->fetchAll(PDO::FETCH_ASSOC);
                    if (isset($_GET['unit'])) {
                        echo "<option value='" . htmlspecialchars($unit) . "' selected> " . htmlspecialchars($unit) . "</option>";
                    }
                    foreach ($units as $unit) {
                        echo "<option value='" . $unit['unitName'] . "'>" . $unit['unitName'] . "</option>";
                    }
                    ?>
                </select>
            </div>
        </div>

        <div class="controller">

            <button onclick="flashOn()" id="btnFlash">💡 Flash Off</button>
            <button onclick="startScanner()" id="btnScan">Scan</button>
        </div>
    </div>

    <script>
        const readerDiv = document.getElementById("reader");
        const result = document.getElementById("result");
        const timeDisplay = document.getElementById("time");
        let timeScan = "";
        const alertDiv = document.getElementById("alert");

        //show result from URL parameters if available
        const urlParams = new URLSearchParams(window.location.search);
        const codeParam = urlParams.get('code');
        const timeParam = urlParams.get('timeScan');
        if (codeParam && timeParam) {
            result.textContent = codeParam;
            timeDisplay.textContent = "Time: " + timeParam;
        }

        // Flashlight control
        const btnFlash = document.getElementById("btnFlash");
        let isTorchOn = false;

        function flashOn() {
            isTorchOn = true;
            btnFlash.textContent = "💡 Flash On";
            btnFlash.onclick = flashOff;
            html5QrCode.applyVideoConstraints({
                torch: true
            }).catch(() => {
                alertDiv.innerHTML = "⚠️ Torch not supported";
            });
        }

        function flashOff() {
            isTorchOn = false;
            btnFlash.textContent = "💡 Flash Off";
            btnFlash.onclick = flashOn;
            html5QrCode.applyVideoConstraints({
                torch: false
            }).catch(() => {});
        }

        // QR Code Scanner configuration
        const html5QrCode = new Html5Qrcode("reader");
        let scanTimeout;
        const config = {
            fps: 20,
            qrbox: {
                width: 300,
                height: 300
            },
            aspectRatio: 1,
            disableFlip: false,
            rememberLastUsedCamera: true,
            showTorchButtonIfSupported: true
        };
        clearTimeout(scanTimeout);

        function onScanFailure(error) {
            // Ignore scan errors
        }

        const btnScan = document.getElementById("btnScan");

        function startScanner() {
            Html5Qrcode.getCameras().then(cameras => {
                btnScan.textContent = "Stop Scanner";
                btnScan.style.color = "red";
                btnScan.onclick = stopScanner;
                result.textContent = "📷 Scanning...";
                timeDisplay.textContent = "";
                if (cameras && cameras.length) {
                    // Prefer back camera
                    let selectedCamera = cameras[0];
                    for (let camera of cameras) {
                        if (camera.label && camera.label.toLowerCase().includes('back')) {
                            selectedCamera = camera;
                            break;
                        }
                    }
                    html5QrCode.start(
                        selectedCamera.id,
                        config,
                        onScanSuccess,
                        onScanFailure
                    ).then(() => {
                        // Apply torch state after scanner successfully starts
                        if (isTorchOn) {
                            html5QrCode.applyVideoConstraints({
                                torch: true
                            }).catch(() => {});
                        }
                    });

                }
                alertDiv.innerHTML = "";
            }).catch(err => {
                alertDiv.innerHTML = "❌ Camera error: " + err;
            });

        }

        function stopScanner() {
            html5QrCode.stop().then(() => {
                btnScan.textContent = "Start Scanner";
                btnScan.style.color = "green";
                result.textContent = "📷 Scanner stopped";
                btnScan.onclick = startScanner;
            });
        }

        function timeNow() {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            return hours + ":" + minutes;
        }

        const inputCode = document.getElementById("inputCode");
        const selectUnit = document.getElementById("unit");
        let unit = selectUnit.value;
        selectUnit.addEventListener("change", () => {
            unit = selectUnit.value;
        });

        function onScanSuccess(decodedText, decodedResult) {
            timeScan = timeNow();
            result.textContent = decodedText;
            timeDisplay.textContent = "Time: " + timeScan;
            console.log(decodedResult);
            // Stop scanning (best-effort), then redirect with code and timeScan params
            html5QrCode.stop().catch(() => {}).then(() => {
                submitCode(decodedText, unit);
            });
        }

        function submitCode(decodedText, unit) {
            timeScan = timeNow();
            if (decodedText) {
                const params = new URLSearchParams({
                    code: decodedText,
                    timeScan: timeScan,
                    unit: unit
                });
                window.location.href = 'codeReader.php?' + params.toString();
            }
        }
        inputCode.addEventListener("keypress", function(event) {
            if (event.key === "Enter") {
                const decodedText = inputCode.value.trim();
                submitCode(decodedText, unit);
            }
        });

        function btnSubmit() {
            const decodedText = inputCode.value.trim();
            submitCode(decodedText, unit);
        }
    </script>

</body>

</html>