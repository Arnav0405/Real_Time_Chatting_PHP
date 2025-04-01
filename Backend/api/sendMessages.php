<?php
session_start();
require_once '../includes/db.php';

header('Content-Type: application/x-www-form-urlencoded');

session_start();
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    die(json_encode(["status" => "error", "message" => "User not logged in"]));
}

echo "Hello World"
echo json_encode(["POST Array" => $_POST])

if ($_SERVER["REQUEST_METHOD"] === "POST"){

    $sender_id = $_SESSION['user_id'];
    $receiver_id = $_POST['receiver_id'] ?? null;
    $message = trim($_POST['message'] ?? '');
    $file_url = isset($_POST['file']) ? $_POST['file'] : null;

    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/'; // Directory to store uploaded files
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true); // Create directory if it doesn't exist
        }

        $fileName = basename($_FILES['file']['name']);
        $filePath = $uploadDir . $fileName;

        // Move the uploaded file to the target directory
        if (move_uploaded_file($_FILES['file']['tmp_name'], $filePath)) {
            $fileUrl = $filePath; // Store the file path
        }
    }

    echo json_encode([
        "receiver_id" => $receiver_id,
        "message" => $message,
        "file_url" => $file_url
    ]);

    if (empty($message) && empty($file_url)) {
        http_response_code(402);
        die(json_encode(["status" => "error", "message" => "Message cannot be empty"]));
    }
    

    try {
        $sql = "INSERT INTO messages (sender_id, receiver_id, message, file_url, status) 
                VALUES (:sender_id, :receiver_id, :message, :file_url, 'sent')";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ":sender_id" => $sender_id,
            ":receiver_id" => $receiver_id,
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