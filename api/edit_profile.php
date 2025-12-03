<?php
session_start();
require_once "../db.php";
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}
$id = $_SESSION['user_id'];

if (empty($_POST['first_name']) || empty($_POST['last_name']) || empty($_POST['username']) || empty($_POST['email']) || empty($_POST['phone']) || empty($_POST['password'])) {
    echo json_encode(['status' => 'error', 'message' => 'Please fill all required fields!']);
    exit;
}

$first_name = $_POST['first_name'];
$last_name = $_POST['last_name'];
$username = $_POST['username'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$password = $_POST['password'];


if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid email format']);
    exit;
}

if (!preg_match('/^[a-zA-Z0-9_]{3,20}$/', $username)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid username']);
    exit;
}

if (!preg_match('/^[0-9]{10,15}$/', $phone)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid phone number']);
    exit;
}

$stmt = $conn->prepare("SELECT * FROM users WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();

if ($user = $res->fetch_assoc()) {
    if (!password_verify($password, $user['password'])) {
        echo json_encode(['status' => 'error', 'message' => 'Current password incorrect!']);
        exit;
    }

    $check_users = $conn->prepare("SELECT id FROM users WHERE (username = ? OR email = ?) AND id != ?");
    $check_users->bind_param("ssi", $username, $email, $id);
    $check_users->execute();
    $res = $check_users->get_result();

    if ($res->num_rows > 0) {
        echo json_encode(['status' => 'error', 'message' => 'Username or email already taken by another user!']);
        exit;
    }
    $update = $conn->prepare("UPDATE users SET first_name=?, last_name=?, username=?, email=?, phone=? WHERE id=?");
    $update->bind_param("sssssi", $first_name, $last_name, $username, $email, $phone, $id);


    if ($update->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Profile updated successfully!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error updating profile!']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => "User not found!"]);
}
