<?php
session_start();
header('Content-Type: application/json');

$servername = 'localhost';
$username = 'root';
$password = '';
$dbname = 'user_management';
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => "Connection failed: " . $conn->connect_error]);
    exit; // Use exit here to stop script execution in case of a connection error
}

$user_id = $_SESSION['user_id']; // Assuming you've stored the user ID in session during login/signup

$interests = json_decode(file_get_contents('php://input'), true)['interests'];

// Clear existing interests for the user
$delete_sql = "DELETE FROM user_interests WHERE user_id = '$user_id'";
if (!$conn->query($delete_sql)) {
    echo json_encode(['success' => false, 'message' => "Error clearing old interests: " . $conn->error]);
    exit; // Stop script execution if there's an error
}



// Default mastery level
$mastery_level = "Beginner";

// Insert new interests with default mastery level
foreach ($interests as $interest) {
    $interest_cleaned = $conn->real_escape_string($interest);
    // Adjusted SQL to include mastery_level
    $sql = "INSERT INTO user_interests (user_id, interest, mastery_level) VALUES ('$user_id', '$interest_cleaned', '$mastery_level')";
    if (!$conn->query($sql)) {
        echo json_encode(['success' => false, 'message' => "Error: " . $sql . "<br>" . $conn->error]);
        exit(); // Stop the script if there's an error inserting new interests
    }
}

echo json_encode(['success' => true]);

$conn->close();
?>
