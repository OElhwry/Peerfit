<link rel="stylesheet" href="match_users.css">

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

    $sql = "SELECT u.id, u.fullname, GROUP_CONCAT(DISTINCT ui.interest ORDER BY ui.interest ASC SEPARATOR ', ') AS interests
        FROM users u
        JOIN user_interests ui ON u.id = ui.user_id
        WHERE u.id != ?
        GROUP BY u.id
        HAVING interests IN (SELECT DISTINCT interest FROM user_interests WHERE user_id = ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $userId, $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    $interestsQuery = "SELECT COUNT(*) AS interest_count FROM user_interests WHERE user_id = $loggedInUserId";
    $interestsResult = $conn->query($interestsQuery);
    $interestsRow = $interestsResult->fetch_assoc();

    if ($interestsRow['interest_count'] == 0) {
        // User has no interests selected
        echo '<p class="welcome-message">Welcome to PeerFit, to get started let\'s start by heading to your <a href="profile.php" target="_top">profile</a> and selecting some sport interests!</p>';
        $conn->close();
        exit; // Stop further execution
    }   

    $loggedInUserLocationQuery = "SELECT location_latitude, location_longitude FROM users WHERE id = $loggedInUserId";
$locationResult = $conn->query($loggedInUserLocationQuery);

if ($locationResult->num_rows === 0) {
    die('Failed to retrieve logged in user location.');
}

$loggedInUserLocation = $locationResult->fetch_assoc();
$loggedInUserLatitude = $loggedInUserLocation['location_latitude'];
$loggedInUserLongitude = $loggedInUserLocation['location_longitude'];


    // Number of users to display per page
    $usersPerPage = 10;

    // Calculate the total number of pages
    $query = "SELECT COUNT(DISTINCT u.id) AS total_users
              FROM users u
              INNER JOIN user_interests ui ON u.id = ui.user_id
              WHERE u.id != $loggedInUserId
              AND ui.interest IN (SELECT interest FROM user_interests WHERE user_id = $loggedInUserId)";

    $result = $conn->query($query);
    $totalUsers = $result->fetch_assoc()['total_users'];
    $totalPages = ceil($totalUsers / $usersPerPage);

    // Get the current page number (from the query string, for example)
    if (isset($_GET['page']) && is_numeric($_GET['page'])) {
        $currentPage = max(1, min($_GET['page'], $totalPages));
    } else {
        $currentPage = 1;
    }

    // Calculate the starting point for retrieving users
    $start = ($currentPage - 1) * $usersPerPage;

    $search = $_GET['search'] ?? '';
    $filter = $_GET['filter'] ?? 'commonInterests'; // Set 'commonInterests' as the default filter
    
    // Dynamic JOIN condition based on the selected filter
    $joinCondition = ($filter === 'followed') ? "INNER JOIN user_follows uf ON uf.receiver_id = u.id AND uf.sender_id = ? AND uf.status = 'accepted'" : "LEFT JOIN user_follows uf ON uf.receiver_id = u.id AND uf.sender_id = ?";
    
    // Common interests subquery that selects only shared interests
    $commonInterestsSubquery = "(
        SELECT GROUP_CONCAT(DISTINCT ui2.interest ORDER BY ui2.interest ASC SEPARATOR ', ')
        FROM user_interests ui2
        INNER JOIN user_interests ui3 ON ui2.interest = ui3.interest AND ui3.user_id = ?
        WHERE ui2.user_id = u.id
    ) AS common_interests";
    
    $query = "
        SELECT u.*,
               $commonInterestsSubquery,
               (6371 * acos(cos(radians(?)) * cos(radians(u.location_latitude)) * cos(radians(u.location_longitude) - radians(?)) + sin(radians(?)) * sin(radians(u.location_latitude)))) AS distance,
               uf.status AS follow_status
        FROM users u
        INNER JOIN user_interests ui ON u.id = ui.user_id
        $joinCondition
        WHERE u.id != ?";
    
    // Parameters initialization
    $params = [$loggedInUserId, $loggedInUserLatitude, $loggedInUserLongitude, $loggedInUserLatitude, $loggedInUserId, $loggedInUserId];
    
    // Add search parameters if applicable
    if (!empty($search)) {
        $query .= " AND (ui.interest LIKE ? OR u.fullname LIKE ?)";
        array_push($params, "%$search%", "%$search%");
    }
    
    // Handle gender filter
    if ($filter === 'male' || $filter === 'female') {
        $query .= " AND u.gender = ?";
        $params[] = $filter;
    }
    
    // If common interests filter is applied, ensure that the common interests are not empty
    if ($filter === 'commonInterests') {
        $query .= " HAVING common_interests IS NOT NULL AND common_interests != ''";
    }
    
    // Order condition
    $orderCondition = ($filter === 'farthest') ? "distance DESC" : "distance ASC";
    $query .= " ORDER BY $orderCondition";
    
    // Prepare, bind, and execute the statement
    $stmt = $conn->prepare($query);
    $types = str_repeat("s", count($params));
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    
?>

<div class="filter-search-form">
    <form action="match_users.php" method="get">
        <input type="text" name="search" placeholder="Search by name or interest" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
        <select name="filter">
            <option value="commonInterests" <?php echo $filter === 'commonInterests' ? 'selected' : ''; ?>>Common Interests</option>
            <option value="nearest" <?php echo $filter === 'nearest' ? 'selected' : ''; ?>>Nearest to Farthest</option>
            <option value="farthest" <?php echo $filter === 'farthest' ? 'selected' : ''; ?>>Farthest to Nearest</option>
            <option value="male" <?php echo $filter === 'male' ? 'selected' : ''; ?>>Male</option>
            <option value="female" <?php echo $filter === 'female' ? 'selected' : ''; ?>>Female</option>
            <option value="followed" <?php echo $filter === 'followed' ? 'selected' : ''; ?>>Followed</option>
        </select>
        <button type="submit">Search</button>
    </form>
</div>
<?php

$uniqueUsers = []; // Initialize an empty array to hold unique users

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $userId = $row['id']; 

        // Check if the user is already in the array
        if (!array_key_exists($userId, $uniqueUsers)) {
            // If not, add the user to the array
            $uniqueUsers[$userId] = $row;
        }
        else {
            $uniqueUsers[$userId] = $row; // Update if needed
        }
    }

// After populating $uniqueUsers, iterate through it to display user information
foreach ($uniqueUsers as $userId => $userInfo) {
    echo "<div class='user-card'>";
    echo "<h2><a href='view_profile.php?user_id=" . $userInfo['id'] . "'>" . htmlspecialchars($userInfo['fullname']) . "</a> (" . round($userInfo['distance'] * 0.621371, 2) . " miles away)</h2>";
    echo "<p>Common Interests: " . htmlspecialchars($userInfo['common_interests']) . "</p>";

    // Determine button text and action based on follow status
    $buttonText = 'Follow';
    $buttonAction = 'follow';
    if ($userInfo['follow_status'] === 'requested') {
        $buttonText = 'Requested';
        $buttonAction = 'unrequest';
    } elseif ($userInfo['follow_status'] === 'accepted') {
        $buttonText = 'Message';
        $buttonAction = 'message'; 
    }
    // Output the button with dynamic text and onclick attribute
    echo "<button id='follow-btn-" . $userInfo['id'] . "' class='message-button' onclick='handleFollowAction(" . $userInfo['id'] . ", \"$buttonAction\")'>" . $buttonText . "</button>";


    echo "</div>"; 
}

 echo '<div class="pagination">';
        for ($page = 1; $page <= $totalPages; $page++) {
            if ($page === $currentPage) {
                echo '<span class="current-page">' . $page . '</span>';
            } else {
                echo '<a href="match_users.php?page=' . $page . '">' . $page . '</a>';
            }
        }
        echo '</div>';
    } else {
        echo '<p>No matching users found.</p>';
    }



$conn->close();

} else {
    echo 'User not logged in.';
}
?>


<script>
function handleFollowAction(userId, action) {
    if(action === 'message') {
        // Redirect the top-level window to chat.php with the userId as a query parameter
        window.top.location.href = `chat.php?user_id=${userId}`;
        return; // Stop further execution for the 'message' action
    }
    
    const formData = new FormData();
    formData.append('user_id', userId);
    formData.append('action', action);

    fetch('handle_follow.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const button = document.getElementById(`follow-btn-${userId}`);
            if (action === 'follow') {
                button.innerText = 'Requested';
                button.setAttribute('onclick', `handleFollowAction(${userId}, 'unrequest')`);
            } else if (action === 'unrequest') {
                button.innerText = 'Follow';
                button.setAttribute('onclick', `handleFollowAction(${userId}, 'follow')`);
            }
            // Handle 'accept' and 'reject' similarly if needed
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}


</script>