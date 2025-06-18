<?php
session_start();

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);  // Enable error reporting

// Retrieve user ID from the URL query string
if (isset($_GET['user_id'])) {
    $userId = intval($_GET['user_id']);
} else {
    echo "No user specified.";
    exit;
}

if (isset($_SESSION['user_id'])) {
    $loggedInUserId = $_SESSION['user_id'];

    // Establish a database connection
    $servername = 'localhost';
    $username = 'root';
    $password = '';
    $dbname = 'user_management';

    $conn = new mysqli($servername, $username, $password, $dbname);

    // Query to fetch user info along with their interests and mastery levels
    $query = "SELECT u.fullname, u.email, u.bio, u.profile_pic, u.gender, GROUP_CONCAT(i.interest ORDER BY i.interest SEPARATOR ', ') AS interests, GROUP_CONCAT(i.mastery_level ORDER BY i.interest SEPARATOR ', ') AS masteries
              FROM users u
              LEFT JOIN user_interests i ON u.id = i.user_id
              WHERE u.id = ?
              GROUP BY u.id";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    $fullname = $email = $bio = $profilePic = "Not available"; // Default values
    $interests = $masteries = "None";
    $interestsAndMasteries = "None";

    if ($row = $result->fetch_assoc()) {
        $fullname = htmlspecialchars($row['fullname']);
        $email = htmlspecialchars($row['email']);
        $bio = htmlspecialchars($row['bio']);
        $gender = htmlspecialchars($row['gender']);
        $profilePic = $row['profile_pic'] ? 'images/'.$row['profile_pic'] : 'images/defaultProfilePic.jpg';
        $interestsArray = explode(", ", $row['interests']);
        $masteriesArray = explode(", ", $row['masteries']);

        // Combine interests and masteries
        $interestsAndMasteries = [];
        foreach ($interestsArray as $index => $interest) {
            $mastery = $masteriesArray[$index] ?? 'Unknown'; // Fallback if count mismatch
            $interestsAndMasteries[] = $interest . " (" . $mastery . ")";
        }
        $interestsAndMasteries = implode(", ", $interestsAndMasteries);
    } else {
        echo "No user found.";
    }
    $stmt->close();
    $conn->close();
} else {
    echo "User not logged in.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="profile.css">
    <title>View Profile - PeerFit</title>
</head>
<body>
    <div class="profile-info-view">
    <img src="<?php echo $profilePic; ?>" alt="Profile Picture" />
        <h1><?php echo $fullname; ?></h1>
        <p>Email:    <?php echo $email; ?></p>
        <p>Gender:    <?php echo $gender; ?></p>
        <p>Bio:    <?php echo $bio; ?></p>
        <p>Interests & Mastery Levels:    <?php echo $interestsAndMasteries; ?></p>
    </div>
</body>
</html>
