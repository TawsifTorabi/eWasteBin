

<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    die("You must log in first to connect to a bin. <a href='login.php'>Login</a>");
}

if (!isset($_GET['session_token'])) {
    die("Invalid or missing session token.");
}

$session_token = $_GET['session_token'];
$user_id = $_SESSION['user_id'];

// Check if token exists
$stmt = $conn->prepare("SELECT * FROM bin_session WHERE token = ? AND user_id IS NULL");
$stmt->bind_param("s", $session_token);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("⚠️ Session already in use or expired.");
}

// Update the session to link it with this user
$update = $conn->prepare("UPDATE bin_session SET user_id = ?, start_time = NOW() WHERE token = ?");
$update->bind_param("is", $user_id, $session_token);
$update->execute();


// Dynamically build the HTTP link
$server_ip = $_SERVER['SERVER_ADDR'] ?? $_SERVER['HTTP_HOST']; // fallback to host
$http_link = "http://{$server_ip}/ewaste/bin_entry.php?session_token=" . urlencode($session_token);

echo "<h2>✅ Bin Connected!</h2>";
echo "<p>You are now linked with the recycle bin session.</p>";
echo "<a href='{$http_link}'>Go to Bin Control</a>";