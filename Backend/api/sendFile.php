<?php
session_start();
require_once '../includes/db.php';

header('Content-Type: application/x-www-form-urlencoded');

session_start();
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    die(json_encode(["status" => "error", "message" => "User not logged in"]));
}


if(isset($_FILES['file'])){
    echo"<pre>";
    print_r($_FILES);
    echo "</pre>";
    $file_name=$_FILES['file']['name'];
    $file_size=$_FILES['file']['size'];
    $file_type=$_FILES['file']['type'];
    $file_tmp=$_FILES['file']['tmp_name'];
    if(move_uploaded_file($file_tmp,"uploaded/". $file_name)){
        echo "uploaded successfully";
    }
    else{
        echo "errors";
    }
   
}

?>