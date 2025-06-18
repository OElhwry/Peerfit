<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

// Database connection
$servername = 'localhost';
$username = 'root';
$password = '';
$dbname = 'user_management';

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => "Connection failed: " . $conn->connect_error]));
}

$user_id = $_SESSION['user_id'];
$interest = $conn->real_escape_string($_POST['interest']);
$mastery_level = $conn->real_escape_string($_POST['masteryLevel']);

$sql = "UPDATE user_interests SET mastery_level = '$mastery_level' WHERE user_id = '$user_id' AND interest = '$interest'";

if ($conn->query($sql) === TRUE) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => "Error updating record: " . $conn->error]);
}

$conn->close();
?>
