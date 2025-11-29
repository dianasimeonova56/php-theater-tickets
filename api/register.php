<?php
require_once "/db.php";

$env = parse_ini_file(__DIR__ . '/.env');
$secretKey = $env['RECAPTCHA_SECRET_KEY'];

$username = $_POST["username"] ?? "";
$first_name = $_POST["first_name"] ?? "";
$last_name = $_POST["last_name"] ?? "";
$email = $_POST["email"] ?? "";
$phone_number = $_POST["phone"] ?? "";
$password = $_POST["password"] ?? "";
$repeat_password = $_POST["repeat_password"] ?? "";
$role = "regular";

if (empty($username) || empty($first_name) || empty($last_name) || empty($email) || empty($phone_number) || empty($password) || empty($repeat_password)) {
    echo json_encode(['status' => 'error', 'message' => "Please fill all fields!"]);
    exit;
}

if ($password != $repeat_password) {
    echo json_encode(['status' => 'error', 'message' => "Passwords do not match!!"]);
    exit;
}

//add captcha
$check_recaptcha = $_POST['recaptcha'];
$verify = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$secretKey}&response={$check_recaptcha}");
$response = json_decode($verify);
if (!$response->success) {
    echo json_encode(['status' => 'error', 'message' => "Confirm you're not a robot!"]);
    exit;
}

$stmt = $conn->prepare("SELECT * FROM users WHERE username = ? OR email = ? LIMIT 1");
$stmt->bind_param("ss", $username, $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo json_encode(['status' => 'error', 'message' => "User already exists!"]);
    exit;
}

$password_hash = password_hash($password, PASSWORD_DEFAULT);
$stmt = $conn->prepare("INSERT INTO users (username, first_name, last_name, email, phone, password, role) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sssssss", $username, $first_name, $last_name, $email, $phone_number, $password_hash, $role);
if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => "Register successful!"]);
    exit;
} else {
    echo json_encode(['status' => 'error', 'message' => "Error during registration!"]);
    exit;
}
