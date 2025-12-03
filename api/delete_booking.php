<?php
require_once "../db.php";

if (empty($_POST['id']) || !is_numeric($_POST['id'])) {
    echo json_encode(["status" => "error", "message" => "No play id provided!"]);
    exit;
}

$id = (int)$_POST['id'];

$stmt = $conn->prepare("DELETE FROM bookings WHERE id = ?");
$stmt->bind_param("i", $id);
if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo json_encode(['status' => 'success', 'message' => 'Booking deleted!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Booking not found!']);
    }
    exit;
} else {
    echo json_encode(['status' => 'error', 'message' => 'Error during booking deletion!']);
    exit;
}
