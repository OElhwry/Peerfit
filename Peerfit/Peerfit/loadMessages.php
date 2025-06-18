<?php
session_start(); // Start the session at the very top of the script

function time_elapsed_string($datetime, $full = false) {
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;

    $string = array(
        'y' => 'year',
        'm' => 'month',
        'w' => 'week',
        'd' => 'day',
        'h' => 'hour',
        'i' => 'minute',
        's' => 'second',
    );
    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
        } else {
            unset($string[$k]);
        }
    }

    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? implode(', ', $string) . ' ago' : 'just now';
}


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

// Check if the session variables are set and not empty
if (isset($_SESSION['user_id']) && isset($_SESSION['chat_partner_id'])) {
    $loggedInUserId = $_SESSION['user_id'];
    $chatPartnerId = $_SESSION['chat_partner_id'];

    // Prepared statement to avoid SQL injection
    $sql = "SELECT m.*, u.fullname as sender_name
            FROM messages m
            JOIN users u ON m.sender_id = u.id
            WHERE (m.sender_id = ? AND m.receiver_id = ?) OR (m.sender_id = ? AND m.receiver_id = ?)
            ORDER BY m.timestamp ASC";

    // Prepare and bind
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiii", $loggedInUserId, $chatPartnerId, $chatPartnerId, $loggedInUserId);

    // Execute and get result
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Output data of each row
        while ($row = $result->fetch_assoc()) {
            // Check if the sender is the logged-in user
            $isOwnMessage = $row["sender_id"] == $loggedInUserId;
            $messageClass = $isOwnMessage ? 'message-own' : 'message-received'; // 'message-received' for styling received messages
            $timestamp = strtotime($row["timestamp"]);
            
            // If the message was sent on a different date, show date, otherwise show time
            $dateOrTime = (date('Y-m-d') == date('Y-m-d', $timestamp)) ? date('g:i A', $timestamp) : date('M j, y', $timestamp);
        
            echo "<div class='message-block {$messageClass}'>";
            // Sender information
            echo "<div class='sender-info'>";
            if ($isOwnMessage) {
                // For your own messages, display "You" and then the date/time
                echo "<span class='message-timestamp'>{$dateOrTime}, You</span>";
            } else {
                // For received messages, display the sender's name and the date/time
                echo "<span class='sender-name'>" . htmlspecialchars($row["sender_name"]) . "</span>";
                echo "<span class='message-timestamp'>{$dateOrTime}</span>";
            }
            echo "</div>";
            
            // Message content
            echo "<div class='message-content'>" . htmlspecialchars($row["message"]) . "</div>";
            echo "</div>";
        }
        

        
    } else {
        echo "<p>No messages found.</p>";
    }

    // Close statement
    $stmt->close();
} else {
    echo "<p>Session variables for user IDs are not set.</p>";
}

// Close connection
$conn->close();
?>
