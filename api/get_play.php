<?php
require_once "../db.php";

if(!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo json_encode(['status' => 'error', 'message' => "No play id provided!"]);
    exit;
}

$id = (int)$_GET['id'];

$stmt = $conn->prepare("SELECT * FROM plays WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
if($result->num_rows > 0) {
    $play = $result->fetch_assoc();
    echo json_encode(['status'=> 'success', 'message' => "Play fetched!", 'data'=>$play]);
    exit;
} else {
    echo json_encode(['status'=> 'error', 'message' => "Play not found!"]);
    exit;
}