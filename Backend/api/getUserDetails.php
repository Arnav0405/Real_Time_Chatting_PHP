<?php
// Backend/api/getUserDetails.php

require_once '../includes/db.php';

$userId = $_GET['userId'] ?? null;

if (!$userId) {
    http_response_code(400);
    echo json_encode(['error' => 'User ID is required']);
    exit;
}

$query = "SELECT id, username, last_seen FROM users WHERE id = :id";
$stmt = $pdo->prepare($query);
$stmt->execute(['id' => $userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user) {
    echo json_encode(['username' => $user['username'], 'last_seen' => $user['last_seen']]);
} else {
    http_response_code(404);
    echo json_encode(['error' => 'User not found']);
}
?>