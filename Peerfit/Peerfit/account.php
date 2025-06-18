<?php
session_start();

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$loggedInUserId = $_SESSION['user_id'];

// Database connection setup
$servername = 'localhost';
$username = 'root';
$password = '';
$dbname = 'user_management';

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Prepare the SQL statement to fetch user details
$query = "SELECT fullname, email, location, gender, bio, profile_pic FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $loggedInUserId);
$stmt->execute();
$result = $stmt->get_result();

// Initialize default values
$fullname = "Not available";
$email = "Not available";
$location = "Not specified";
$gender = "Not specified";
$bio = "No bio available.";
$profilePic = "images/defaultProfilePicture.jpg"; // Default profile picture

if ($row = $result->fetch_assoc()) {
    $fullname = htmlspecialchars($row['fullname']);
    $email = htmlspecialchars($row['email']);
    $location = htmlspecialchars($row['location']);
    $gender = htmlspecialchars($row['gender']);
    $bio = htmlspecialchars($row['bio']);
    if (!empty($row['profile_pic']) && file_exists('images/' . $row['profile_pic'])) {
        $profilePic = 'images/' . htmlspecialchars($row['profile_pic']);
    }
} else {
    echo "User not found.";
    exit;
}

$stmt->close();
$conn->close();
?>



<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
  <link rel="stylesheet" href="account.css">
  <script src="sidebar.js"></script>
  <title>Account Page - PeerFit</title>
</head>
<body>

  <div class="container">
    <div class="sidebar">
      <div class="icon">
          <a href="home.html"><img src="Images/PeerFitLogo.jpg" alt="PeerFit Logo" width="100%" height="100%"></a>
      </div>

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



    <div class="content">
    <div class="account-info">
        <h2>Account Settings</h2>
        <div class="profile-pic-container">
            <img src="<?php echo $profilePic; ?>" alt="Profile Picture" class="profile-pic" id="profilePreview" />
            <form action="upload_profile_pic.php" method="post" enctype="multipart/form-data" class="upload-form">
                <input type="file" name="newProfilePic" id="newProfilePic" class="inputfile" onchange="previewImage();" />
                <label for="newProfilePic">Change Picture</label>
                <button type="submit" class="btn-upload">Upload</button>
            </form>
        </div>
        <form action="update_account.php" method="post">
            <label for="fullname">Full Name:</label>
            <input type="text" id="fullname" name="fullname" value="<?php echo htmlspecialchars($fullname); ?>" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>

            <label for="password">Password (leave blank to not change):</label>
            <input type="password" id="password" name="password" placeholder="New Password">

            <label for="location">Location:</label>
            <input type="text" id="location" name="location" value="<?php echo htmlspecialchars($location); ?>" required>

            <label for="gender">Gender:</label>
            <select id="gender" name="gender" required>
                <option value="Male" <?php echo ($gender == 'Male') ? 'selected' : ''; ?>>Male</option>
                <option value="Female" <?php echo ($gender == 'Female') ? 'selected' : ''; ?>>Female</option>
            </select>
            <label for="bio">Bio:</label>
            <textarea id="bio" name="bio"><?php echo htmlspecialchars($row['bio']); ?></textarea>



            <input type="submit" value="Update">
        </form>
    </div>
</div>
</body>


<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA45ZFlxM0vD53MyLNF05UN8aa88tsiHpk&libraries=places&callback=initAutocomplete" async defer></script>
<script>



  // Initialize Google Places Autocomplete
  function initAutocomplete() {
    var input = document.getElementById('location');
    var options = {
      types: ['geocode'] // restricts the search to geographical location types
    };

    var autocomplete = new google.maps.places.Autocomplete(input, options);
}

function previewImage() {
    var file = document.getElementById("newProfilePic").files;
    if (file.length > 0) {
        // A file has been selected
        var fileReader = new FileReader();

        fileReader.onload = function(event) {
            document.getElementById("profilePreview").setAttribute("src", event.target.result);
        };

        fileReader.readAsDataURL(file[0]);
    }
}




</script>
</html>