<?php
require_once "../db.php";

$env = parse_ini_file(__DIR__ . '/../.env');
$secretKey = $env['RECAPTCHA_SECRET_KEY'];

$username = $_POST["username"] ?? "";
$first_name = $_POST["first_name"] ?? "";
$last_name = $_POST["last_name"] ?? "";
$email = $_POST["email"] ?? "";
$phone_number = $_POST["phone"] ?? "";
$role = "regular";

if (empty($username) || empty($first_name) || empty($last_name) || empty($email) || empty($phone_number)) {
    echo json_encode(['status' => 'error', 'message' => "Please fill all fields!"]);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['status' => 'error', 'message' => "Invalid email format!"]);
    exit;
}

if (!preg_match('/^[0-9]{10,15}$/', $phone_number)) {
    echo json_encode(['status' => 'error', 'message' => "Invalid phone number!"]);
    exit;
}

$passwordError = "";
$password = htmlspecialchars($_POST["password"]);
$confirmPassword = htmlspecialchars($_POST["repeat_password"]);
if (empty($_POST['password']) || empty($_POST['repeat_password'])) {
    $passwordError = "Provide password and confirm password!";
} else {
    if ($password != $confirmPassword) {
        $passwordError .= "Passwords are not same.\n";
    }
    if (strlen($password) < 8) {
        $passwordError .= "Password must have 8 characters at least.\n";
    }
    if (!preg_match("#[0-9]+#", $password)) {
        $passwordError .= "Password must have 1 Number at least.\n";
    }
    if (!preg_match("#[A-Z]+#", $password)) {
        $passwordError .= "Password must have 1 uppercase letter at least.\n";
    }
    if (!preg_match("#[a-z]+#", $password)) {
        $passwordError .= "Password must have 1 lowercase letter at least.\n";
    }
}

if ($passwordError != "") {
    echo json_encode(['status' => 'error', 'message' => $passwordError]);
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

$stmt = $conn->prepare("SELECT * FROM users WHERE username = ? OR email = ? OR phone = ? LIMIT 1");
$stmt->bind_param("sss", $username, $email, $phone_number);
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
