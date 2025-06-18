<?php
session_start();

header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "user_management";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => "Connection failed: " . $conn->connect_error]);
    exit;
}

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => "You must be logged in to perform this action."]);
    exit;
}

$userId = $_SESSION['user_id'];

if (isset($_POST['follow_id'], $_POST['action'])) {
    $followId = $_POST['follow_id'];
    $action = $_POST['action'];

    switch ($action) {
        case 'accept':
            $sql = "UPDATE user_follows SET status = 'accepted' WHERE id = ? AND receiver_id = ?";
            break;
        case 'reject':
            $sql = "DELETE FROM user_follows WHERE id = ? AND receiver_id = ?";
            break;
        case 'unrequest': // Handling the unrequest action
            $sql = "DELETE FROM user_follows WHERE id = ? AND sender_id = ?"; // Adjust according to your logic
            break;
        default:
            echo json_encode(['success' => false, 'message' => "Invalid action"]);
            exit;
    }

    $stmt = $conn->prepare($sql);
    if ($stmt) {
        // Adjust the binding parameters based on the action
        if ($action === 'unrequest') {
            $stmt->bind_param("ii", $followId, $userId); // For unrequest, the current user is the sender
        } else {
            $stmt->bind_param("ii", $followId, $userId); // For accept/reject, the current user is the receiver
        }

        if ($stmt->execute()) {
            $message = $action === 'accept' ? "Follow request accepted." : ($action === 'reject' ? "Follow request rejected." : "Follow request cancelled.");
            echo json_encode(['success' => true, 'message' => $message]);
        } else {
            echo json_encode(['success' => false, 'message' => "Failed to update follow request."]);
        }
        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => "Query preparation failed."]);
    }
} else {
    echo json_encode(['success' => false, 'message' => "Invalid request"]);
}

$conn->close();
?>