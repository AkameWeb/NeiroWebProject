<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

session_start();

// Подключение к базе данных
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
$name = $data['name'] ?? '';
$email = $data['email'] ?? '';
$password = $data['password'] ?? '';
$passwordConfirm = $data['passwordConfirm'] ?? '';
$remember = $data['remember'] ?? false;

// Валидация данных
if (empty($name) || empty($email) || empty($password) || empty($passwordConfirm)) {
    echo json_encode(['success' => false, 'message' => 'Все поля обязательны для заполнения']);
    exit;
}

if ($password !== $passwordConfirm) {
    echo json_encode(['success' => false, 'message' => 'Пароли не совпадают']);
    exit;
}

if (strlen($password) < 6) {
    echo json_encode(['success' => false, 'message' => 'Пароль должен содержать минимум 6 символов']);
    exit;
}

// Проверка существования пользователя
$stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'Пользователь с таким email уже существует']);
    exit;
}

// Хеширование пароля
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Создание пользователя
$stmt = $conn->prepare("INSERT INTO users (username, email, password, created_at) VALUES (?, ?, ?, NOW())");
$stmt->bind_param("sss", $name, $email, $hashedPassword);

if ($stmt->execute()) {
    $user_id = $stmt->insert_id;
    
    // Создание сессии
    $_SESSION['user_id'] = $user_id;
    $_SESSION['username'] = $name;
    $_SESSION['email'] = $email;
    
    $response = [
        'success' => true,
        'message' => 'Регистрация успешна',
        'user' => [
            'id' => $user_id,
            'username' => $name,
            'email' => $email
        ]
    ];
    
    // Если стоит галочка "Запомнить меня"
    if ($remember) {
        $token = bin2hex(random_bytes(32));
        $expires = time() + (30 * 24 * 60 * 60);
        
        $stmt = $conn->prepare("UPDATE users SET remember_token = ?, token_expires = ? WHERE id = ?");
        $stmt->bind_param("ssi", $token, date('Y-m-d H:i:s', $expires), $user_id);
        $stmt->execute();
        
        setcookie('remember_token', $token, $expires, '/');
        setcookie('user_id', $user_id, $expires, '/');
    }
    
    echo json_encode($response);
} else {
    echo json_encode(['success' => false, 'message' => 'Ошибка при регистрации: ' . $conn->error]);
}

$stmt->close();
$conn->close();
?>