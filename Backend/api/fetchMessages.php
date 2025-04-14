<?php 
require_once '../includes/db.php';

// header('Content-Type: application/json');
session_start();
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    die(json_encode(["status" => "error", "message" => "User not logged in"]));
}

$sender_id = $_SESSION['user_id'];
$receiver_id = $_GET['receiver_id']; 

try {
    $sql = "SELECT * FROM messages 
            WHERE (sender_id = :sender_id AND receiver_id = :receiver_id) 
               OR (sender_id = :receiver_id AND receiver_id = :sender_id)
            ORDER BY sent_at ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ":sender_id" => $sender_id,
        ":receiver_id" => $receiver_id
    ]);
    
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $formattedMessages = array_map(function ($msg) {
        return [
            'text' => $msg['message'],
            'fileUrl' => $msg['file_url'],
            'sender' => $msg['sender_id'] == $_SESSION['user_id'] ? 'sent' : 'received',
            'time' => date('h:i A', strtotime($msg['sent_at']))
        ];
    }, $messages);

    echo json_encode(['success' => true, 'messages' => $formattedMessages]);
} catch (PDOException $e) {
    http_response_code(405);
    echo json_encode(["status" => false, "message" => "Error fetching messages: " . $e->getMessage()]);
}
?>