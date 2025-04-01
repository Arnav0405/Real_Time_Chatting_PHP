<?php
session_start();

// Ensure session is active and user is logged in
if (!isset($_SESSION['user_id'])) {
    error_log('Unauthorized access attempt detected.');
    http_response_code(401);
    echo json_encode(['error' => 'User not logged in']);
    exit;
}

// Include database connection
require_once '../includes/db.php';

// Validate PDO instance
if (!isset($pdo) || !$pdo instanceof PDO) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection not established']);
    exit;
}

try {
    // Prepare and execute query
    $query = "SELECT id, username, bio FROM users WHERE id != :this_user_id";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['this_user_id' => $_SESSION['user_id']]);
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Return users as JSON
    echo json_encode(['users' => $users]);

    // Check for JSON encoding errors
    if (json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to encode response data']);
        exit;
    }
} catch (PDOException $e) {
    // Log and handle database errors
    error_log('Database error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'An internal server error occurred']);
}
?>