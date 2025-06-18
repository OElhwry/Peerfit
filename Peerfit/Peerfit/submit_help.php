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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $report = $_POST['report'];
    $user_id = $_SESSION['user_id']; // Assuming the user's ID is stored in session

    // SQL to insert data
    $sql = "INSERT INTO help (user_id, report) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("is", $user_id, $report);
        $stmt->execute();
        echo "Report submitted successfully.";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
}
?>
