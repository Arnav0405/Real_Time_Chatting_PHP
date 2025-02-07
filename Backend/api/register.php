<?php

require_once(dirname(__FILE__) ."../includes/db.php");

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents(dirname(__FILE__) .'php://input'), true);

    $username = trim($data['username']);
    $password = trim($data['password']);

    if (empty($username) || empty($password)) {
        echo json_encode(['error' => 'Username and password are required']);
        exit;
    }


    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    try {
        $statement = $pdo->prepare("SELECT id, username, password FROM users WHERE username = :username");
        $statement->execute(['username' => $username]);
        $user = $statement->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            echo json_encode(['message' => 'Login successful']);
        } else {
            echo json_encode(['error' => 'Invalid credentials']);
        }
    } catch (PDOException $e) {
        echo json_encode(['error' => 'Username already exists']);
    }
} else {
    echo json_encode(['error' => 'Invalid request method']);
}