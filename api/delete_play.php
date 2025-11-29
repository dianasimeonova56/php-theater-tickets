<?php
require_once "/db.php";

if (empty("id")) {
    echo json_encode(["status" => "error", "message" => "No play id provided!"]);
    exit;
}

$id = $_POST['id'];

$stmt = $conn->prepare("DELETE FROM plays WHERE id = ?");
$stmt->bind_param("i", $id);
if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo json_encode(['status' => 'success', 'message' => 'Play deleted!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Play not found!']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Error during play deletion!']);
    exit;
}
