<?php 

require_once '../includes/db.php';

header('Content-Type: application/json');


if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $statement = $pdo->query("SELECT m.id, u.username, m.message, m.created_at 
                             FROM messages m 
                             JOIN users u ON m.sender_id = u.id 
                             ORDER BY m.created_at ASC");
        $messages = $statement->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(['messages' => $messages]);
    } catch (PDOException $e) {
        echo json_encode(['error' => 'Failed to fetch messages']);
    }
} else {
    echo json_encode(['error' => 'Invalid request method']);
}
?>