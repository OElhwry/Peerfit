<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id'];
$servername = 'localhost';
$username = 'root';
$password = '';
$dbname = 'user_management';

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['newProfilePic']) && $_FILES['newProfilePic']['error'] == UPLOAD_ERR_OK) {
    $uploadDir = 'images/';
    $originalFileName = basename($_FILES['newProfilePic']['name']);
    $fileType = strtolower(pathinfo($originalFileName, PATHINFO_EXTENSION));

    // Append user ID and a timestamp to the filename to ensure uniqueness
    $newFileName = $userId . "_" . time() . "." . $fileType;
    $uploadFile = $uploadDir . $newFileName;

    // Check for allowed file types
    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
    if (!in_array($fileType, $allowedTypes)) {
        echo "Only JPG, JPEG, PNG & GIF files are allowed.";
        exit;
    }

    $check = getimagesize($_FILES['newProfilePic']['tmp_name']);
    if ($check !== false) {
        // Move the uploaded file to its new path
        if (move_uploaded_file($_FILES['newProfilePic']['tmp_name'], $uploadFile)) {
            $stmt = $conn->prepare("UPDATE users SET profile_pic = ? WHERE id = ?");
            $stmt->bind_param("si", $newFileName, $userId);
            if ($stmt->execute()) {
                header("Location: account.php?update=success"); // Redirect on success
                exit();
            } else {
                echo "Error updating record: " . $conn->error;
            }
            $stmt->close();
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    } else {
        echo "File is not an image.";
    }
} else {
    echo "No file uploaded or upload error.";
}


$conn->close();
?>
