<?php
require_once("../includes/db.php");
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $data = json_decode(file_get_contents('php://input'), true);
    if (!isset($data['username']) || !isset($data['email']) || !isset($data['password'])) {
        http_response_code(400);
        echo json_encode(['message' => 'Missing required fields']);
        exit;
    }
    // Get form data
    $username   = trim($data['username']);
    $email      = trim($data['email']);
    $password   = trim($data['password']);

    // echo json_encode(["username" => $username, "emai" => $email, "password" => $password]);
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    try {
        $statement = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (:username, :email, :password)");
        $statement->execute(['username' => $username, 'email' => $email, 'password' => $hashed_password]);
        
        http_response_code(201);
        echo json_encode(["message" => "User Succesfully Registered"]);
    } catch (PDOException $e) {
        http_response_code(409);
        echo json_encode(['error' => 'Username or email already exists']);
    }
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Invalid request method']);
}
?>