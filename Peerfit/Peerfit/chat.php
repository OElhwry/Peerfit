<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="chat.css">
  <script src="sidebar.js"></script>
  <title>Chat - PeerFit</title>
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
          </a>
            </div>


</div>
</div>
<div class="user-selection-sidebar">
<?php
session_start(); // Start the session if it's not already started

// Check if a user is logged in
if (isset($_SESSION['user_id'])) {
    // The user is logged in, retrieve their ID from the session
    $loggedInUserId = $_SESSION['user_id'];

    // Establish a database connection
    $servername = 'localhost';
    $username = 'root';
    $password = '';
    $dbname = 'user_management';

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Fetch matched users or friends list
// Fetch matched users or friends list with accepted follow status
$query = "SELECT u.id, u.fullname
          FROM users u
          JOIN user_follows uf ON (uf.receiver_id = u.id AND uf.sender_id = $loggedInUserId AND uf.status = 'accepted')
               OR (uf.sender_id = u.id AND uf.receiver_id = $loggedInUserId AND uf.status = 'accepted')
          GROUP BY u.id, u.fullname
          ORDER BY u.fullname ASC";


    $result = $conn->query($query);

    echo '<div id="chat-container">'; // Container for the chat area
    echo '<h2>Chat</h2>'; // Chat heading
    echo '<div id="user-search">';
    echo '    <input type="text" id="searchInput" placeholder="Search users..." autocomplete="off"/>';
    echo '</div>'; // Search bar
    
    if ($result->num_rows > 0) {
        echo '<div id="user-list">'; // Container for the user list
        while ($user = $result->fetch_assoc()) {
            echo '<div class="user" data-user-id="' . htmlspecialchars($user['id']) . '">' . htmlspecialchars($user['fullname']) . '</div>';
        }
        echo '</div>'; // Close user list container
    } else {
        echo '<p>No users found.</p>';
    }
    echo '</div>'; // Close chat container
}
    

?>

</div>

<div class="chat-container">
  <div class="messages" id="messageContainer"></div>
  <form id="messageForm">
    <input type="text" id="messageInput" placeholder="Type a message..." autocomplete="off"/>
    <button type="submit">Send</button>
  </form>
</div>

</body>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="script.js"></script>

