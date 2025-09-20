<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

session_start();

// Подключение к базе данных (замените на свои данные)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "music_search";

// Создание соединения
$conn = new mysqli($servername, $username, $password, $dbname);

// Проверка соединения
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Connection failed: ' . $conn->connect_error]));
}

// Получение данных из POST запроса
$data = json_decode(file_get_contents('php://input'), true);
$email = $data['email'] ?? '';
$password = $data['password'] ?? '';
$remember = $data['remember'] ?? false;

// Валидация данных
if (empty($email) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Все поля обязательны для заполнения']);
    exit;
}

// Поиск пользователя в базе данных
$stmt = $conn->prepare("SELECT id, username, password FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Пользователь не найден']);
    exit;
}

$user = $result->fetch_assoc();

// Проверка пароля
if (password_verify($password, $user['password'])) {
    // Создание сессии
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['email'] = $email;
    
    // Подготовка ответа
    $response = [
        'success' => true,
        'message' => 'Авторизация успешна',
        'user' => [
            'id' => $user['id'],
            'username' => $user['username'],
            'email' => $email
        ]
    ];
    
    // Если стоит галочка "Запомнить меня", устанавливаем куки
    if ($remember) {
        $token = bin2hex(random_bytes(32));
        $expires = time() + (30 * 24 * 60 * 60); // 30 дней
        
        // Сохраняем токен в базе данных
        $stmt = $conn->prepare("UPDATE users SET remember_token = ?, token_expires = ? WHERE id = ?");
        $stmt->bind_param("ssi", $token, date('Y-m-d H:i:s', $expires), $user['id']);
        $stmt->execute();
        
        // Устанавливаем куки
        setcookie('remember_token', $token, $expires, '/');
        setcookie('user_id', $user['id'], $expires, '/');
    }
    
    echo json_encode($response);
} else {
    echo json_encode(['success' => false, 'message' => 'Неверный пароль']);
}

$stmt->close();
$conn->close();
?>