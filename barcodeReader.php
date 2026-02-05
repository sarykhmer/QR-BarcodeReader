<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Barcode & QR Scanner</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- html5-qrcode library -->
    <script src="https://unpkg.com/html5-qrcode"></script>

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
            margin-top: 10px;
            padding: 10px 20px;
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
            border-radius: 25px;
            width: 150px;
            height: 100px;
        }
    </style>
</head>

<body>
    <div class="container">
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
        <div class="reload">
            <button onclick="location.reload()">🔄 Reload Page</button>
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

        function onScanSuccess(decodedText, decodedResult) {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            timeScan = hours + ":" + minutes;

            result.textContent = decodedText;
            timeDisplay.textContent = "Time: " + timeScan;
            console.log(decodedResult);
            // Stop scanning (best-effort), then redirect with code and timeScan params
            html5QrCode.stop().catch(() => {}).then(() => {
                const params = new URLSearchParams({
                    code: decodedText,
                    timeScan: timeScan
                });
                window.location.href = 'barcodeReader.php?' + params.toString();
            });
        }
    </script>

</body>

</html>