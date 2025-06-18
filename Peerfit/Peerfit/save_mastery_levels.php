<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

$servername = 'localhost';
$username = 'root';
$password = '';
$dbname = 'user_management';
$user_id = $_SESSION['user_id'];

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => "Database connection failed: " . $conn->connect_error]);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
if (empty($data['updates'])) {
    echo json_encode(['success' => false, 'message' => 'No updates received']);
    exit;
}

foreach ($data['updates'] as $update) {
    $interest = $conn->real_escape_string($update['interest']);
    $masteryLevel = $conn->real_escape_string($update['masteryLevel']);
    $sql = "UPDATE user_interests SET mastery_level='$masteryLevel' WHERE user_id='$user_id' AND interest='$interest'";
    if (!$conn->query($sql)) {
        echo json_encode(['success' => false, 'message' => "Failed to update interest: " . $conn->error]);
        exit;
    }
}

echo json_encode(['success' => true, 'message' => 'Mastery levels updated successfully']);
$conn->close();
?>
