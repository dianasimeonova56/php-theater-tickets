<?php
session_start();
require_once "../db.php";

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}
$id = $_SESSION['user_id'];

if (empty($_POST['password']) || empty($_POST['new_password'])) {
    echo json_encode(['status' => 'error', 'message' => 'Please fill all required fields!']);
    exit;
}

$password = $_POST['password'];
$new_password = $_POST['new_password'];
$passwordError = "";

$stmt = $conn->prepare("SELECT * FROM users WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();

if ($user = $res->fetch_assoc()) {
    if (!password_verify($password, $user['password'])) {
        echo json_encode(['status' => 'error', 'message' => 'Current password incorrect!']);
        exit;
    }

    if (strlen($new_password) < 8) {
        $passwordError .= "Password must have 8 characters at least.\n";
    }
    if (!preg_match("#[0-9]+#", $new_password)) {
        $passwordError .= "Password must have 1 Number at least.\n";
    }
    if (!preg_match("#[A-Z]+#", $new_password)) {
        $passwordError .= "Password must have 1 uppercase letter at least.\n";
    }
    if (!preg_match("#[a-z]+#", $new_password)) {
        $passwordError .= "Password must have 1 lowercase letter at least.\n";
    }

    if ($passwordError == "") {
        $hash = password_hash($new_password, PASSWORD_DEFAULT);
        $update = $conn->prepare("UPDATE users SET password=? WHERE id =?");
        $update->bind_param("si", $hash,  $id);

        if ($update->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Password updated successfully!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error updating password!']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => $passwordError]);
        exit;
    }
} else {
    echo json_encode(['status' => 'error', 'message' => "User not found!"]);
}
