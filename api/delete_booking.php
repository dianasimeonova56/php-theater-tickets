<?php
require_once "/db.php";

if (empty("id")) {
    echo json_encode(["status" => "error", "message" => "No booking id provided!"]);
    exit;
}

$id = $_POST['id'];

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
