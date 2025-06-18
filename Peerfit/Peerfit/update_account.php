<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header("Location: login.php");
    exit;
}

// Database connection parameters
$servername = 'localhost';
$username = 'root';
$password = '';
$dbname = 'user_management';

// Create database connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$loggedInUserId = $_SESSION['user_id'];

// Get the posted form data
$fullname = $conn->real_escape_string($_POST['fullname']);
$email = $conn->real_escape_string($_POST['email']);
$password = $_POST['password']; // Not escaping yet because it will be hashed
$location = $conn->real_escape_string($_POST['location']);
$gender = $conn->real_escape_string($_POST['gender']);
$bio = $conn->real_escape_string($_POST['bio']);

// Check if password was provided
if (!empty($password)) {
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $updateQuery = "UPDATE users SET fullname=?, email=?, password=?, location=?, gender=?, bio=? WHERE id=?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("ssssssi", $fullname, $email, $hashedPassword, $location, $gender, $bio, $loggedInUserId);
} else {
    // Update without changing the password
    $updateQuery = "UPDATE users SET fullname=?, email=?, location=?, gender=?, bio=? WHERE id=?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("sssssi", $fullname, $email, $location, $gender, $bio, $loggedInUserId);
}


if ($stmt->execute()) {
    echo "Account updated successfully.";
    header("Location: account.php?update=success"); // Redirect on success
} else {
    echo "Error updating account: " . $conn->error;
}


$stmt->close();
$conn->close();
?>
