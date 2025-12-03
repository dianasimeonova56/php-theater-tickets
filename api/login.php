<?php
session_start();
require_once "../db.php";

$env = parse_ini_file(__DIR__ . '/../.env');
$secretKey = $env['RECAPTCHA_SECRET_KEY'];

$usernameOrEmail = $_POST["usernameOrEmail"] ?? "";
$password = $_POST["password"] ?? "";

if (empty($usernameOrEmail) || empty($password)) {
    echo json_encode(['status' => 'error', 'message' => "Please fill all fields!"]);
    exit;
}

$check_recaptcha = $_POST['recaptcha'];
$verify = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$secretKey}&response={$check_recaptcha}");
$response = json_decode($verify);
if (!$response->success) {
    echo json_encode(['status' => 'error', 'message' => "Confirm you're not a robot"]);
    exit;
}

if (strpos($usernameOrEmail, '@') !== false) { 
    if (!filter_var($usernameOrEmail, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['status' => 'error', 'message' => "Invalid email format!"]);
        exit;
    }
}

$stmt = $conn->prepare("SELECT * FROM users WHERE email = ? OR username = ? LIMIT 1");
$stmt->bind_param("ss", $usernameOrEmail, $usernameOrEmail);
$stmt->execute();
$result = $stmt->get_result();

if ($user = $result->fetch_assoc()) {
    if (password_verify($password, $user['password'])) {
        $_SESSION['username'] = $user['username'];
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_role'] = $user['role'];
        echo json_encode(['status' => 'success', 'message' => 'Login successful!']);
        exit;
    } else {
        echo json_encode(['status' => 'error', 'message' => "Invalid credentials!"]);
        exit;
    }
} else {
    echo json_encode(['status' => 'error', 'message' => "Invalid credentials!"]);
    exit;
}
