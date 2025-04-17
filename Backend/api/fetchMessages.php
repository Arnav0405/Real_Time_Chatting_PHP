<?php 
require_once '../includes/db.php';

// header('Content-Type: application/json');
session_start();
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    die(json_encode(["status" => "error", "message" => "User not logged in"]));
}

$sender_id = $_SESSION['user_id'];
$reciever_id = $_GET['reciever_id']; 

try {
    $sql = "SELECT * FROM messages 
            WHERE (sender_id = :sender_id AND reciever_id = :reciever_id) 
               OR (sender_id = :reciever_id AND reciever_id = :sender_id)
            ORDER BY sent_at ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ":sender_id" => $sender_id,
        ":reciever_id" => $reciever_id
    ]);
    
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $formattedMessages = array_map(function ($msg) {
        return [
            'text' => $msg['message'],
            'fileUrl' => $msg['file_url'],
            'sender' => $msg['sender_id'] == $_SESSION['user_id'] ? 'sent' : 'received',
            'time' => $msg['sent_at'],
        ];
    }, $messages);

    // Update the status of messages from "sent" to "read" where the current user is the receiver
    $updateSql = "UPDATE messages 
                SET status = 'read' 
                WHERE reciever_id = :user_id 
                    AND sender_id = :other_user_id 
                    AND status = 'sent'";
    $updateStmt = $pdo->prepare($updateSql);
    $updateStmt->execute([
                            ":user_id" => $sender_id,
                            ":other_user_id" => $reciever_id
                        ]);


    echo json_encode(['success' => true, 'messages' => $formattedMessages]);
} catch (PDOException $e) {
    http_response_code(405);
    echo json_encode(["status" => false, "message" => "Error fetching messages: " . $e->getMessage()]);
}
?>