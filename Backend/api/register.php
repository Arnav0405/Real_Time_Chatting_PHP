<?php
require_once("../includes/db.php");
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($email) || empty($password)) {
        echo $password;
        echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
        exit;
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    try {
        $statement = $pdo->prepare("SELECT * FROM users WHERE username = :username OR email = :email");
        $statement->execute(['username' => $username, 'email' => $email]);
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
?>