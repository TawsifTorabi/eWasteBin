<?php
require_once 'db_connect.php';

$user_id = $_POST['user_id'] ?? null;
$session_id = $_POST['session_id'] ?? null;
$class_label = $_POST['class_label'] ?? null;
$confidence = $_POST['confidence'] ?? null;
$image_data = $_POST['image'] ?? null;

if (!$user_id || !$class_label || !$image_data) {
    die("Missing data.");
}

$image_name = "uploads/" . time() . "_user{$user_id}.jpg";
file_put_contents($image_name, base64_decode($image_data));

$stmt = $conn->prepare("INSERT INTO detections (user_id, session_id, class_label, confidence, image_path) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("iisss", $user_id, $session_id, $class_label, $confidence, $image_name);
$stmt->execute();

echo "Data stored successfully.";
?>
