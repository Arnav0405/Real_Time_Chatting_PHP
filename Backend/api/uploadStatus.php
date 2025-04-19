<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    die(json_encode(["status" => "error", "message" => "User not logged in"]));
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "No file uploaded"]);
        exit;
    }

    $uploadDir = __DIR__ . '/uploaded/statuses/';

    $fileName = uniqid('status_', true) . '_' . basename($_FILES['file']['name']);
    $filePath = $uploadDir . $fileName;

    if (move_uploaded_file($_FILES['file']['tmp_name'], $filePath)) {
        $fileUrl = 'http://localhost/WPL/WPL_Project/Backend/api/uploaded/statuses/' . $fileName; // Relative path for the frontend

        try {
            $sql = "INSERT INTO statuses (user_id, file_url) VALUES (:user_id, :file_url)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ":user_id" => $_SESSION['user_id'],
                ":file_url" => $fileUrl
            ]);

            http_response_code(201);
            echo json_encode(["status" => "success", "message" => "Status uploaded successfully"]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
        }
    } else {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => "Failed to save file"]);
    }
} else {
    http_response_code(405);
    echo json_encode(["status" => "error", "message" => "Invalid request method"]);
}
?>