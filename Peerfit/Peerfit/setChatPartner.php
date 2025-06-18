<?php
session_start();

if(isset($_POST['chat_partner_id'])) {
    $_SESSION['chat_partner_id'] = $_POST['chat_partner_id'];
    echo json_encode(["status" => "success"]);
} else {
    echo json_encode(["status" => "error", "message" => "Chat partner ID not provided"]);
}


?>
