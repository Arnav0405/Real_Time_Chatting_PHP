<?php
session_start();
require_once '../includes/db.php';

try {
    $sql = "
        SELECT s.id, s.file_url, s.created_at, u.username 
        FROM statuses s
        JOIN users u ON s.user_id = u.id
        ORDER BY s.created_at DESC
    ";
    $stmt = $pdo->query($sql);
    $statuses = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $formattedStatuses = array_map(function ($status) {
        return [
            'username' => $status['username'],
            'fileUrl' => $status['file_url'],
            'createdAt' => date('h:i A', strtotime($status['created_at']))
        ];
    }, $statuses);

    echo json_encode(["status" => "success", "statuses" => $formattedStatuses]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
}
?>