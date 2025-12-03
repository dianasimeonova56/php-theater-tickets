<?php
require_once "../db.php";

$stmt = $conn->prepare("SELECT * FROM plays");
$stmt->execute();
$result = $stmt->get_result();

if (!$result) {
    echo json_encode(['status' => 'error', 'message' => $conn->error]);
    exit;
}

if ($result->num_rows === 0) {
    echo json_encode(['status' => 'success', 'data' => []]);
    exit;
}

$plays = [];
while ($row = $result->fetch_assoc()) {
    $plays[] = $row;
}

echo json_encode(['status' => 'success', 'data' => $plays]);
