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

// Processing the follow/unfollow requests
if (isset($_POST['action']) && isset($_POST['user_id'])) {
    $targetUserId = $_POST['user_id'];
    $action = $_POST['action'];

    switch ($action) {
        case 'follow':
            // Attempt to insert a new follow request
            $sql = "INSERT INTO user_follows (sender_id, receiver_id, status) VALUES (?, ?, 'requested')";
            $stmt = $conn->prepare($sql);
            if ($stmt) {
                $stmt->bind_param("ii", $userId, $targetUserId);
                if ($stmt->execute()) {
                    echo json_encode(['success' => true, 'message' => "Follow request sent."]);
                } else {
                    // Check for duplicate entry error code
                    if ($conn->errno == 1062) {
                        echo json_encode(['success' => false, 'message' => "Follow request already exists."]);
                    } else {
                        echo json_encode(['success' => false, 'message' => "Failed to send follow request."]);
                    }
                }
                $stmt->close();
            } else {
                echo json_encode(['success' => false, 'message' => "Failed to prepare the statement."]);
            }
            break;

        case 'unrequest':
            // Cancel an existing follow request
            $query = "DELETE FROM user_follows WHERE sender_id=? AND receiver_id=? AND status='requested'";
            $stmt = $conn->prepare($query);
            if ($stmt) {
                $stmt->bind_param("ii", $userId, $targetUserId);
                $stmt->execute();
                if ($stmt->affected_rows > 0) {
                    echo json_encode(['success' => true, 'message' => "Request cancelled."]);
                } else {
                    echo json_encode(['success' => false, 'message' => "No request to cancel."]);
                }
                $stmt->close();
            } else {
                echo json_encode(['success' => false, 'message' => "Query preparation failed."]);
            }
            break;

        case 'accept':
            // Accept a follow request
            $query = "UPDATE user_follows SET status='accepted' WHERE sender_id=? AND receiver_id=? AND status='requested'";
            $stmt = $conn->prepare($query);
            if ($stmt) {
                $stmt->bind_param("ii", $targetUserId, $userId);
                $stmt->execute();
                echo json_encode(['success' => true, 'message' => "Follow request accepted."]);
                $stmt->close();
            } else {
                echo json_encode(['success' => false, 'message' => "Query preparation failed."]);
            }
            break;

        case 'reject':
            // Reject a follow request
            $query = "DELETE FROM user_follows WHERE sender_id=? AND receiver_id=? AND status='requested'";
            $stmt = $conn->prepare($query);
            if ($stmt) {
                $stmt->bind_param("ii", $targetUserId, $userId);
                $stmt->execute();
                echo json_encode(['success' => true, 'message' => "Follow request rejected."]);
                $stmt->close();
            } else {
                echo json_encode(['success' => false, 'message' => "Query preparation failed."]);
            }
            break;

        default:
            echo json_encode(['success' => false, 'message' => "Invalid action"]);
            break;
    }
} else {
    echo json_encode(['success' => false, 'message' => "Invalid request"]);
}

$conn->close();
?>
