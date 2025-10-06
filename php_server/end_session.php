<?php
session_start();
require_once 'db_connect.php';

// Accept session token (from GET or POST)
$token = $_GET['token'] ?? null;

if (!$token) {
    die("No session token provided.");
}

// Find the bin session
$stmt = $conn->prepare("SELECT * FROM bin_session WHERE token = ? AND user_id IS NOT NULL AND end_time IS NULL");
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Session not found or already ended.");
}

$session = $result->fetch_assoc();
$session_id = $session['id'];
$user_id = $session['user_id'];

// --- Step 1: Calculate rewards ---
$reward_points = 0;
$detection_result = $conn->query("SELECT class_label FROM detections WHERE session_id = $session_id");

while ($row = $detection_result->fetch_assoc()) {
    // Example reward rules
    switch (strtolower($row['class_label'])) {
        case 'phone': $reward_points += 5; break;
        case 'laptop': $reward_points += 10; break;
        case 'battery': $reward_points += 3; break;
        default: $reward_points += 1; break;
    }
}

// --- Step 2: Update user points ---
$conn->query("UPDATE user SET points = points + $reward_points WHERE id = $user_id");

// --- Step 3: Close the session ---
$stmt = $conn->prepare("UPDATE bin_session SET end_time = NOW() WHERE id = ?");
$stmt->bind_param("i", $session_id);
$stmt->execute();

echo "<h2>Session Ended ✅</h2>";
echo "<p>User earned <b>$reward_points points</b>.</p>";
echo "<p><a href='thankyou.php'>Go to Thank You Page</a></p>";

// Optional: destroy session vars
unset($_SESSION['active_session_id']);
unset($_SESSION['active_user_id']);
?>
