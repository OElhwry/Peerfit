  <!DOCTYPE html>
  <html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="profile.css">
    <script src="sidebar.js"></script>
    <title>Profile Page - PeerFit</title>
  </head>
  <body>
    <div class="background-shade"></div>





      <div class="profile-info">
        <?php
        session_start();

        if (isset($_SESSION['user_id'])) {
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

        $query = "SELECT fullname, email, bio, profile_pic, gender FROM users WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $loggedInUserId);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if ($row = $result->fetch_assoc()) {
            $fullname = htmlspecialchars($row['fullname']);
            $email = htmlspecialchars($row['email']);
            $bio = htmlspecialchars($row['bio'] ?? 'No bio available'); // Fallback if bio is empty
            $profilePic = $row['profile_pic'] ? 'images/' . $row['profile_pic'] : 'images/defaultProfilePic.jpg';
            $gender = htmlspecialchars($row['gender']);
            
            // Display the profile information including the picture
            echo "<img src='" . $profilePic . "' alt='Profile Picture' style='width: 150px; height: 150px; border-radius: 50%;'>";
            echo "<p>Name: $fullname</p>";
            echo "<p>Gender: $gender</p>";
            echo "<p>Email: $email</p>";
            echo "<p>Bio: $bio</p>";
        } else {
            echo "User not found.";
            exit;
        }
        $stmt->close();


        if (isset($_SESSION['email'])) {
            $email = $_SESSION['email'];

            // Fetch user's full name based on email
            $sql = "SELECT fullname FROM users WHERE email='$email'";
            $result = $conn->query($sql);

            if ($result->num_rows == 1) {
                $row = $result->fetch_assoc();
                $fullname = $row['fullname'];

            }

                  // Fetching and displaying interested sports from the database

        $sql_sports = "SELECT sport_name FROM user_interests WHERE email='$email'";
        $result_sports = $conn->query($sql_sports);

      // Query to fetch user's interests
      $interestQuery = "SELECT interest, mastery_level FROM user_interests WHERE user_id = $loggedInUserId";
      $result = $conn->query($interestQuery);

// After fetching interests from the database
if ($result && $result->num_rows > 0) {
  echo "<p>Interested sports:</p><ul>";
  while ($row = $result->fetch_assoc()) {
      // Ensure the structure allows for easy JavaScript manipulation
      echo "<li class='interest-item'><span class='interest-text'>" . htmlspecialchars($row['interest']) . "</span> - <span class='mastery-level'>" . htmlspecialchars($row['mastery_level']) ?? 'Beginner' . "</span></li>";
  }
  echo "</ul>";

  // Display buttons only if there are interests
  if ($result->num_rows > 0) {
      echo "<div class='edit-buttons'>";
      echo "<button id='editProfile' onclick=\"location.href='account.php'\">Edit Profile</button>";
      echo "<button onclick=\"location.href='edit_interests.php'\">Edit Interests</button>";
      echo "<button id='editMastery'>Edit Mastery</button>";
      echo "</div>";
  }
} else {
  echo "<p>No interests added yet. <a href='edit_interests.php'>Add some now!</a></p>";
}



    

      echo "</ul>";

      $conn->close();
  } else {
    echo "You are not logged in.";
  }}
        ?>
      </div>
    </div>

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
            
    </body>
    <script src="script.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA45ZFlxM0vD53MyLNF05UN8aa88tsiHpk&callback=initMap"></script>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const editMasteryButton = document.getElementById('editMastery');
    let isEditing = false;

    editMasteryButton.addEventListener('click', function() {
        const interestItems = document.querySelectorAll('.interest-item');
        if (!isEditing) {
            interestItems.forEach(item => {
                const masteryLevelSpan = item.querySelector('.mastery-level');
                const currentMastery = masteryLevelSpan.textContent.trim();
                const dropdown = document.createElement('select');
                ['Beginner', 'Intermediate', 'Advanced'].forEach(level => {
                    const option = document.createElement('option');
                    option.value = option.textContent = level;
                    if (level === currentMastery) option.selected = true;
                    dropdown.appendChild(option);
                });
                masteryLevelSpan.textContent = '';
                masteryLevelSpan.appendChild(dropdown);
            });
            editMasteryButton.textContent = 'Save Changes';
            isEditing = true;
        } else {
            const updates = Array.from(interestItems).map(item => {
                return {
                    interest: item.querySelector('.interest-text').textContent,
                    masteryLevel: item.querySelector('select').value
                };
            });
            fetch('save_mastery_levels.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({updates: updates})
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    interestItems.forEach(item => {
                        const select = item.querySelector('select');
                        const masteryLevelSpan = item.querySelector('.mastery-level');
                        masteryLevelSpan.textContent = select.value;
                        select.remove();
                    });
                    editMasteryButton.textContent = 'Edit Mastery';
                    isEditing = false;
                } else {
                    alert('Failed to update: ' + data.message);
                }
            })
            .catch(error => console.error('Error:', error));
        }
    });
});
</script>




    </html>
