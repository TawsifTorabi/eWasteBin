<?php
require_once 'db_connect.php';

// Generate a unique session token for this bin
$session_token = bin2hex(random_bytes(8)); // Example: 16-char unique token
$bin_id = 1; // static if this QR is tied to a specific bin

// Save it to DB (creates waiting bin session)
$stmt = $conn->prepare("INSERT INTO bin_session (user_id, start_time, token) VALUES (NULL, NOW(), ?)");
$stmt->bind_param("s", $session_token);
$stmt->execute();

// Generate URL for scanning
$scan_url = "https://192.168.137.1/ewaste/scan.php?session_token=" . urlencode($session_token);

// External QR API (QRServer)
$qr_api = "https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=" . urlencode($scan_url);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Smart Bin QR</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      text-align: center;
      background: #eef4fa;
      margin-top: 80px;
    }
    .card {
      background: #fff;
      padding: 40px;
      display: inline-block;
      border-radius: 12px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    img {
      margin-top: 20px;
      border: 8px solid #007bff;
      border-radius: 12px;
    }
  </style>
</head>
<body>
  <div class="card">
    <h2>📱 Scan to Connect</h2>
    <p>Use your phone’s app to scan and start your recycling session.</p>
    <img src="<?php echo $qr_api; ?>" alt="QR Code">
    <p><small>Session Token: <?php echo htmlspecialchars($session_token); ?></small></p>
  </div>
</body>
</html>
