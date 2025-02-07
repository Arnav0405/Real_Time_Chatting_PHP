<?php
session_start();
require_once '../includes/db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['error' => 'Unauthorized']);
        exit;
    }

    $data = json_decode(file_get_contents('php://input'), true);
    $message = trim($data['message']);

    if (empty($message)) {
        echo json_encode(['error' => 'Message cannot be empty']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO messages (sender_id, message) VALUES (:sender_id, :message)");
        $stmt->execute(['sender_id' => $_SESSION['user_id'], 'message' => $message]);

        echo json_encode(['message' => 'Message sent successfully']);
    } catch (PDOException $e) {
        echo json_encode(['error' => 'Failed to send message']);
    }
} else {
    echo json_encode(['error' => 'Invalid request method']);
}
?>