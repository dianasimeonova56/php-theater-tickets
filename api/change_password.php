<?php
session_start();
require_once "/db.php";

if (empty($_POST['password']) || empty($_POST['new_password'])) {
    echo json_encode(['status' => 'error', 'message' => 'Please fill all required fields!']);
    exit;
}

$id = $_SESSION['user_id'];
$password = $_POST['password'];
$new_password = $_POST['new_password'];

$stmt = $conn->prepare("SELECT * FROM users WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();

if ($user = $res->fetch_assoc()) {
    if (!password_verify($password, $user['password'])) {
        echo json_encode(['status' => 'error', 'message' => 'Current password incorrect!']);
        exit;
    }

    $hash = password_hash($new_password, PASSWORD_DEFAULT);
    $update = $conn->prepare("UPDATE users SET password=? WHERE id =?");
    $update->bind_param("si", $hash,  $id);

    if ($update->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Password updated successfully!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error updating password!']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => "User not found!"]);
}
