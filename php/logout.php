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

if (!$conn->connect_error && isset($_SESSION['user_id'])) {
    $stmt = $conn->prepare("UPDATE users SET remember_token = NULL, token_expires = NULL WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $stmt->close();
}

// Очистка сессии
session_unset();
session_destroy();

// Очистка куки
setcookie('remember_token', '', time() - 3600, '/');
setcookie('user_id', '', time() - 3600, '/');

echo json_encode(['success' => true, 'message' => 'Logged out successfully']);
$conn->close();
?>