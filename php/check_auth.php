<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

session_start();

// Подключение к базе данных
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "music_search";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Connection failed']));
}

// Проверка сессии
if (isset($_SESSION['user_id'])) {
    $stmt = $conn->prepare("SELECT id, username, email FROM users WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        echo json_encode(['success' => true, 'user' => $user]);
        exit;
    }
}

// Проверка remember me куки
if (isset($_COOKIE['remember_token']) && isset($_COOKIE['user_id'])) {
    $stmt = $conn->prepare("SELECT id, username, email FROM users WHERE id = ? AND remember_token = ? AND token_expires > NOW()");
    $stmt->bind_param("is", $_COOKIE['user_id'], $_COOKIE['remember_token']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];
        
        echo json_encode(['success' => true, 'user' => $user]);
        exit;
    }
}

echo json_encode(['success' => false, 'message' => 'Not authenticated']);
$conn->close();
?>