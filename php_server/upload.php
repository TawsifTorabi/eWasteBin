<?php
//Legacy code for uploading images and storing inference results
// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "inference_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "DB Connection failed: " . $conn->connect_error]));
}

// Read raw input
$data = json_decode(file_get_contents("php://input"), true);

if (!$data || !isset($data["image"]) || !isset($data["inference"])) {
    echo json_encode(["status" => "error", "message" => "Invalid data"]);
    exit;
}

$inference = $conn->real_escape_string($data["inference"]);
$imageData = $data["image"];

// Decode Base64 image
$image = base64_decode($imageData);
$filename = "img_" . time() . ".jpg";
$filePath = "uploads/" . $filename;

// Ensure uploads folder exists
if (!file_exists("uploads")) {
    mkdir("uploads", 0777, true);
}

// Save image to disk
file_put_contents($filePath, $image);

// Insert record into database
$sql = "INSERT INTO inferences (filename, inference, created_at) VALUES ('$filename', '$inference', NOW())";

if ($conn->query($sql) === TRUE) {
    echo json_encode(["status" => "success", "filename" => $filename]);
} else {
    echo json_encode(["status" => "error", "message" => $conn->error]);
}

$conn->close();
?>
