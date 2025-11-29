<?php
require_once "/db.php";

$sql = "SELECT * FROM plays";
$result = $conn->query($sql);

if (!$result) {
    echo json_encode(['status' => 'error', 'message' => "No plays found"]);
    exit;
}

$plays = [];
while ($row = $result->fetch_assoc()) {
    $plays[] = $row;
}

echo json_encode(['status' => 'success', 'data' => $plays]);
