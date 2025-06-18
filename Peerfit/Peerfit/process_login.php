<?php
// process_login.php

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

// Retrieve form data
$email = $_POST['email'];
$password = $_POST['password'];

// Fetch user from the database based on email
$sql = "SELECT * FROM users WHERE email='$email'";
$result = $conn->query($sql);

if ($result->num_rows == 1) {
    $row = $result->fetch_assoc();
    if (password_verify($password, $row['password'])) {
        // Start the session
        session_start();

        // Set user's session variables
        $_SESSION['user_id'] = $row['id'];  // Assuming your user ID column is 'id'
        $_SESSION['email'] = $email;

        // Check if location is set
        if ($row['location_latitude'] != 0 && $row['location_longitude'] != 0) {
            // Redirect to home.html
            header("Location: home.html");
            exit();
        } else {
            // Redirect to home.html (you can modify this to a different page if needed)
            header("Location: home.html");
            exit();
        }
    } else {
        // Redirect with error message for invalid password
        header("Location: login.php?error=invalid_password");
        exit();
    }
} else {
    // Redirect with error message for user not found
    header("Location: login.php?error=user_not_found");
    exit();
}
?>
