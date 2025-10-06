<?php
session_start();
require_once 'db_connect.php';

// Check login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Scan QR - Smart E-Waste Bin</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
body { font-family: Arial; text-align:center; margin:0; padding:0; background:#eef4fa; }
h2 { margin-top:20px; }
#reader { width: 100%; max-width: 400px; margin: 20px auto; }
#log { margin-top:15px; font-size:16px; color:#333; word-break: break-word; }
</style>

<!-- HTML5 QR Code library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.8/html5-qrcode.min.js" integrity="sha512-r6rDA7W6ZeQhvl8S7yRVQUKVHdexq+GAlNkNNqVC7YyIV+NwqCTJe2hDWCiffTyRNOeGEzRRJ9ifvRm/HCzGYg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

</head>
<body>
<h2>Hello, <?php echo htmlspecialchars($user_name); ?> 👋</h2>
<p>Scan the QR code on the bin to connect:</p>
<div id="reader"></div>
<div id="log">Waiting for QR scan...</div>

<script>
window.onload = function() {
    const logDiv = document.getElementById('log');
    let scanned = false; // flag to prevent multiple redirects

    function onScanSuccess(decodedText, decodedResult) {
        logDiv.innerHTML = "✅ QR scanned: <a href='" + decodedText + "'>" + decodedText + "</a>";

        if (!scanned) {
            scanned = true; // prevent multiple redirects
            // Pause scanning for 1 sec before redirect to let user see scanned info
            setTimeout(() => {
                html5QrcodeScanner.clear().then(_ => {
                    window.location.href = encodeURIComponent(decodedText);
                }).catch(err => console.error(err));
            }, 1000);
        }
    }

    function onScanFailure(error) {
        // optional: show scanning errors if needed
        // logDiv.innerHTML = "Scanning...";
    }

    // Initialize scanner
    var html5QrcodeScanner = new Html5Qrcode("reader");

    html5QrcodeScanner.start(
        { facingMode: "environment" }, 
        { fps: 10, qrbox: 250 },
        onScanSuccess,
        onScanFailure
    ).catch(err => {
        logDiv.innerHTML = "❌ Camera access denied or not supported.";
        console.error(err);
    });
};
</script>
</body>
</html>
