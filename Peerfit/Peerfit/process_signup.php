<?php
session_start();

$servername = 'localhost';
$username = 'root';
$password = '';
$dbname = 'user_management';

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$fullname = $_POST['fullname'];
$email = $_POST['email'];
$password = $_POST['password'];
$confirm_password = $_POST['confirmPassword'];
$gender = isset($_POST['gender']) ? $conn->real_escape_string($_POST['gender']) : null;

if ($password !== $confirm_password) {
    $_SESSION['error'] = "Passwords don't match.";
    header("Location: signup.php");
    exit();
}

$hashed_password = password_hash($password, PASSWORD_BCRYPT);

$location_data = geocodeAddress($_POST['address']);

if ($location_data && isset($location_data['geometry']['location'])) {
    $location_formatted_address = $location_data['formatted_address'];
    $location_latitude = $location_data['geometry']['location']['lat'];
    $location_longitude = $location_data['geometry']['location']['lng'];
} else {
    $location_formatted_address = '';
    $location_latitude = 0;
    $location_longitude = 0;
}

$sql = "INSERT INTO users (fullname, email, password, location_latitude, location_longitude, location, gender, profile_pic) 
        VALUES ('$fullname', '$email', '$hashed_password', $location_latitude, $location_longitude, '$location_formatted_address', '$gender', 'DefaultProfilePicture.jpg')";


if ($conn->query($sql) === TRUE) {
    $user_id = $conn->insert_id;  // Get the ID of the inserted user

    // Store user interests
    if (isset($_POST['interests']) && is_array($_POST['interests'])) {
        foreach ($_POST['interests'] as $interest) {
            $interest = mysqli_real_escape_string($conn, $interest);
            $interest_sql = "INSERT INTO user_interests (user_id, interest) VALUES ('$user_id', '$interest')";
            $conn->query($interest_sql);
        }
    }

    $_SESSION['fullname'] = $fullname;
    $_SESSION['email'] = $email;

    header("Location: Success_Signup.html");
    exit();
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();

function geocodeAddress($address) {
    $apiKey = 'AIzaSyA45ZFlxM0vD53MyLNF05UN8aa88tsiHpk';  // Replace with your actual API key
    $address = urlencode($address);

    $url = "https://maps.googleapis.com/maps/api/geocode/json?address={$address}&key={$apiKey}";

    $response = file_get_contents($url);

    if ($response === FALSE) {
        // Error occurred while fetching the response
        error_log('Geocoding request failed: ' . error_get_last()['message']);
        return null;
    }

    $jsonData = json_decode($response, true);

    if ($jsonData === null || $jsonData['status'] !== 'OK') {
        // Geocoding failed
        error_log('Geocoding request failed: ' . json_encode($jsonData));
        return null;
    }

    return $jsonData['results'][0];
}
?>
