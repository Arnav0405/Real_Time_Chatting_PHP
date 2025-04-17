<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    die(json_encode(["status" => "error", "message" => "User not logged in"]));
}

if ($_SERVER["REQUEST_METHOD"] === "POST"){

    $sender_id = $_SESSION['user_id'];

    $reciever_id = $_POST['reciever_id'] ?? null;
    if ($reciever_id == null) {
        http_response_code(403);
        die(json_encode(["status" => "error", "message" => "Cannot send message to noone."]));
    }

    $message = trim($_POST['message'] ?? '');
    $fileUrl = null;
    
    // File Uploading Part
    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/uploaded/';
        // echo __DIR__. '/uploads/'; // Debugging

        $fileName = uniqid('file_', true) . '_' . $sender_id . '_' . $reciever_id;
        $filePath = $uploadDir . $fileName;
        
test
        if (move_uploaded_file($_FILES['file']['tmp_name'], $filePath)) {
            $fileUrl = '/uploaded/' . $fileName; // Relative path for the frontend
            
        } else {
            http_response_code(502);
            echo $_FILES['file']['tmp_name'];
            echo $filePath;
            echo json_encode(['error' => false, 'message' => 'Failed to save file']);
            exit;
        }
    }

    try {
        $sql = "INSERT INTO messages (sender_id, reciever_id, message, file_url, status) 
                VALUES (:sender_id, :reciever_id, :message, :file_url, 'sent')";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ":sender_id" => $sender_id,
            ":reciever_id" => $reciever_id,
            ":message" => $message,
            ":file_url" => $fileUrl
        ]);

        http_response_code(201);
        echo json_encode(["status" => "success", "message" => "Message sent"]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => "Error sending message: " . $e->getMessage()]);
    }
} else {
    http_response_code(405);
    echo json_encode(["status" => "error", "message" => "Invalid request"]);
}
?>