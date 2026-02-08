<?php
require 'checkRole.php';
if (!isset($_SESSION['recordID'])) {
    header("Location: createRecord_form.php");
    exit();
}

$recordID = $_SESSION['recordID'];
$recordType = $_SESSION['recordType'];
if (isset($_GET['code']) && isset($_GET['timeScan'])) {

    $code = trim($_GET['code']);
    $timeScan = $_GET['timeScan'];
    $unit = $_GET['unit'] ?? '1pc';

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
    } elseif ($exist && $recordType == 1) {
        // if code exist, update dTime
        try {
            $stmt_update = $pdo->prepare("UPDATE tblDetail SET dTime = ? WHERE recordID = ? AND sNumber = ?");
            $stmt_update->execute([$timeScan, $recordID, $code]);
        } catch (PDOException $e) {
            // Handle the error, e.g., log it and show a user-friendly message
            error_log("Database error: " . $e->getMessage());
            die("An error occurred while updating the record! <br> " . $e);
        }
    } elseif ($exist && $recordType == 2) {
        // if code exist, update rTime
        try {
            $stmt_update = $pdo->prepare("UPDATE tblDetail SET cgTime = ? WHERE recordID = ? AND sNumber = ?");
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
            position: relative;
            width: 100%;
            max-width: 600px;
            margin: auto;
        }

        .input {
            margin-top: 20px;
            width: 100%;
        }

        #unit {
            width: 20%;
            font-size: 18px;
            float: left;
        }

        #inputCode {
            width: 60%;
            font-size: 18px;
            text-align: center;
            float: left;
        }

        #btnSubmit {
            width: 18%;
            background-color: green;
            border-radius: 10px;
        }

        #reader {
            width: 100%;
            height: 500px;
            font-size: 25px;
            color: green;
        }

        #result {
            margin-top: 200px;
            position: absolute;
            z-index: 1000;
            color: green;
        }

        #time {
            margin-top: 260px;
            position: absolute;
            z-index: 1000;
            color: green;
        }

        .result {
            width: 100%;
            align-items: center;
            display: flex;
            justify-content: center;
        }

        button {
            font-size: 18px;
        }

        .scan,
        .flash {
            width: 100%;
            z-index: 999;
            position: relative;
        }

        #btnScan {
            color: green;
            border: none;
            border-radius: 50%;
            width: 150px;
            height: 150px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="input">
            <select name="unit" id="unit">
                <?php
                $retrive_unit = $pdo->prepare("SELECT * FROM tblUnit");
                $retrive_unit->execute();
                $units = $retrive_unit->fetchAll(PDO::FETCH_ASSOC);
                foreach ($units as $unit) {
                    echo "<option value='" . $unit['unitName'] . "'>" . $unit['unitName'] . "</option>";
                }
                ?>
            </select>

            <input type="text" id="inputCode" oninput="this.value=this.value.replace(/[^0-9]/g,'')">
            <button id="btnSubmit" onclick="submitCode()">➡️</button>
        </div>
        <div class="result">
            <h2 id="result">📷 Barcode / QR Code Scanner</h2>
            <h2 id="time"></h2>
        </div>
        <div id="reader">
        </div>
        <div id="alert"></div>
        <div class="flash">
            <button onclick="flashOn()" id="btnFlash">💡 Flash Off</button>
        </div>
        <div class="scan">
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
        const decodedText = inputCode.value.trim();
        const unit = document.getElementById("unit").value;

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
                submitCode(inputCode.value.trim(), unit);
            }
        });
    </script>

</body>

</html>