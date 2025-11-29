<?php
session_start();
require_once "/db.php";

if (empty($_POST['first_name']) || empty($_POST['last_name']) || empty($_POST['username']) || empty($_POST['email']) || empty($_POST['phone']) || empty($_POST['password'])) {
    echo json_encode(['status' => 'error', 'message' => 'Please fill all required fields!']);
    exit;
}

$id = $_SESSION['user_id'];
$first_name = $_POST['first_name'];
$last_name = $_POST['last_name'];
$username = $_POST['username'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$password = $_POST['password'];

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
