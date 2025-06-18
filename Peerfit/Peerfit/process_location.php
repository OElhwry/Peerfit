<?php
// process_set_location.php

session_start();

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

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $location_data = json_decode($_POST['location'], true);

    // Extract location details
    $formatted_address = $location_data['formatted_address'];
    $latitude = $location_data['geometry']['location']['lat'];
    $longitude = $location_data['geometry']['location']['lng'];

    // Update the user's location in the database
    $sql = "UPDATE users SET location_formatted_address='$formatted_address', location_latitude='$latitude', location_longitude='$longitude' WHERE user_id=$user_id";

    if ($conn->query($sql) === TRUE) {
        echo "Location updated successfully.";
    } else {
        echo "Error updating location: " . $conn->error;
    }
} else {
    echo "You are not logged in.";
}

$conn->close();
?>
