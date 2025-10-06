<?php
require_once 'db_connect.php';

if (!isset($_GET['session_token'])) {
    die("Invalid access: no token.");
}

$token = $_GET['session_token'];

// Find active session
$stmt = $conn->prepare("SELECT s.*, u.name FROM bin_session s LEFT JOIN user u ON s.user_id = u.id WHERE s.token = ?");
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Session not found.");
}

$session = $result->fetch_assoc();
$user_name = $session['name'];
$user_id = $session['user_id'];
$session_id = $session['id'];

if (!$user_id) {
    die("Bin not yet paired with a user.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Connected Bin</title>
  <style>
    body { font-family: Arial; text-align: center; margin-top: 70px; background: #f0f6ff; }
    .card { background: white; padding: 30px; display: inline-block; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
    button { margin: 10px; padding: 10px 20px; border: none; border-radius: 8px; background: #007bff; color: white; cursor: pointer; }
    button:hover { background: #0056b3; }
  </style>
</head>
<body>
  <div class="card">
    <h2>Welcome, <?php echo htmlspecialchars($user_name); ?> 👋</h2>
    <p>Connected successfully to the bin.</p>
    <button onclick="startCapture()">Capture Item</button>
    <button onclick="endSession()">End Session</button>
    <div id="log"></div>
  </div>

  <script>
    const logDiv = document.getElementById('log');
    async function startCapture(){
        logDiv.innerHTML = "Capturing and analyzing...";
        try {
            const res = await fetch('http://192.168.137.1:5000/capture?user_id=<?php echo $user_id; ?>&session_id=<?php echo $session_id; ?>');
            const data = await res.json();
            if(data.success){
                logDiv.innerHTML = `<b>${data.result}</b> (${data.confidence.toFixed(2)}%)<br><img src="data:image/jpeg;base64,${data.image}" width="250">`;
            } else {
                logDiv.innerHTML = "❌ Capture failed.";
            }
        } catch (e){
            logDiv.innerHTML = "❌ Flask connection error.";
        }
    }

    async function endSession(){
        await fetch('end_session.php?token=<?php echo urlencode($token); ?>');
        alert("Session ended. Thank you!");
        location.href = 'thankyou.php';
    }
  </script>
</body>
</html>
