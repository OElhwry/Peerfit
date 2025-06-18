<?php
session_start();

// Check if both message and chat_partner_id are set
if(isset($_POST['message'], $_SESSION['chat_partner_id'], $_SESSION['user_id'])) {
    $message = $_POST['message'];
    $senderId = $_SESSION['user_id'];
    $receiverId = $_SESSION['chat_partner_id'];

    // Database connection variables
    $servername = 'localhost';
    $username = 'root';
    $password = '';
    $dbname = 'user_management';

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Prepare statement to insert the message
    $stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $senderId, $receiverId, $message);

    // Execute the statement
    if ($stmt->execute()) {
        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to send message"]);
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();
} else {
    echo json_encode(["status" => "error", "message" => "Required fields are missing"]);
}
?>
