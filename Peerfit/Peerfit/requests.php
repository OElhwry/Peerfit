<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    echo 'You must be logged in to view this page.';
    exit;
}

$userId = $_SESSION['user_id'];
$servername = 'localhost';
$username = 'root';
$password = '';
$dbname = 'user_management';
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch incoming follow requests
$incomingSql = "SELECT uf.id AS follow_id, u.fullname, uf.sender_id
                FROM user_follows uf
                JOIN users u ON uf.sender_id = u.id
                WHERE uf.receiver_id = ? AND uf.status = 'requested'";
$incomingStmt = $conn->prepare($incomingSql);
$incomingStmt->bind_param("i", $userId);
$incomingStmt->execute();
$incomingResult = $incomingStmt->get_result();

// Fetch sent follow requests
$sentSql = "SELECT uf.id AS follow_id, u.fullname, uf.receiver_id
            FROM user_follows uf
            JOIN users u ON uf.receiver_id = u.id
            WHERE uf.sender_id = ? AND uf.status = 'requested'";
$sentStmt = $conn->prepare($sentSql);
$sentStmt->bind_param("i", $userId);
$sentStmt->execute();
$sentResult = $sentStmt->get_result();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="requests.css">
    <script src="sidebar.js"></script>
    <title>Requests - PeerFit</title>
</head>
<body>
  <div class="sidebar">
      <div class="icon">
          <a href="home.html"><img src="Images/PeerFitLogo.jpg" alt="PeerFit Logo" width="100%" height="100%"></div></a>

          <a href="home.html">
        <div class="logo">
          <img src="Images/home.png" alt="Logo 1" width="30">
        </div>
        Home
      </a>
      
          <a href="#profile">
              <div class="logo">
                <img src="Images/User.png" alt="Logo 1" width="30">
              </div>
              Profile
            </a>
            
          
            <a href="chat.php">
              <div class="logo">
                <img src="Images/Chat.png" alt="Logo 2" width="30">
              </div>
              Chat
            </a>
          
            <a href="requests.php">
              <div class="logo">
                <img src="Images/Request.png" alt="Logo 3" width="30">
              </div>
              Requests
            </a>

          
            <a href="Startup.html">
              <div class="logo">
                <img src="Images/SignOut.png" alt="PeerFit Logo" width="30">
              </div>
              Sign out
            </a>

          </div>

          <div class="secondary-sidebar">
            <h2>Account</h2>
            <a href="Profile.php">
              <div class="item">
                <div class="logo"><img src="Images/User_grey.png" alt="Profile Logo" width="30"></div>
                <span>Profile</span>
              </div>
            </a>
            <a href="account.php">
              <div class="item">
                <div class="logo"><img src="Images/lock.png" alt="Account Logo" width="30"></div>
                <span>Account</span>
              </div>
            </a>  
            </div>
            <div class="requests-container">
            <section class="requests-received">
    <h2>Requests Received</h2>
    <?php if ($incomingResult->num_rows > 0): ?>
        <?php while ($row = $incomingResult->fetch_assoc()): ?>
            <div>
    <p><?php echo htmlspecialchars($row['fullname']); ?> wants to follow you.</p>
    <button onclick="handleFollowRequest(<?php echo $row['follow_id']; ?>, 'accept')">Accept</button>
    <button onclick="handleFollowRequest(<?php echo $row['follow_id']; ?>, 'reject')">Reject</button>
</div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No follow requests received.</p>
    <?php endif; ?>
</section>

        
<section class="requests-sent">
    <h2>Requests Sent</h2>
    <?php if ($sentResult->num_rows > 0): ?>
        <?php while ($row = $sentResult->fetch_assoc()): ?>
            <div class="request-item">
                <p>You sent a follow request to <?php echo htmlspecialchars($row['fullname']); ?>.</p>
                <!-- X button to cancel request -->
                <button class="cancel-request-btn" onclick="cancelFollowRequest(<?php echo $row['follow_id']; ?>, this)">X</button>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No follow requests sent.</p>
    <?php endif; ?>
</section>

    </div>
</body>
<script>
function handleFollowRequest(followId, action) {
    const formData = new FormData();
    formData.append('follow_id', followId);
    formData.append('action', action);

    fetch('handle_request.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);
        // Optionally, refresh the page to show the updated state or update the UI directly without refreshing.
        window.location.href = 'requests.php';
    })
    .catch(error => console.error('Error:', error));
}


function cancelFollowRequest(followId, element) {
    if (!confirm('Are you sure you want to cancel this follow request?')) return;

    const formData = new FormData();
    formData.append('follow_id', followId);
    formData.append('action', 'unrequest'); // Make sure 'unrequest' is handled in your PHP script

    fetch('handle_request.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Refresh the page to show the updated state
            window.location.reload();
        } else {
            alert(data.message);
        }
    })
    .catch(error => console.error('Error:', error));
}


</script>
</html>

