<?php
require_once "../db.php";

if (empty($_GET['playId']) || !is_numeric($_GET['playId'])) {
    echo json_encode(['status' => 'error', 'message' => 'No play id provided!']);
    exit;
}

$resultArr = [];
$id = $_GET['playId'];

$stmt = $conn->prepare("SELECT id FROM bookings WHERE play_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$bookings = $stmt->get_result();
while ($booking = $bookings->fetch_assoc()) {
    $stmt = $conn->prepare("SELECT seat_number FROM tickets WHERE booking_id = ?");
    $stmt->bind_param("i", $booking['id']);
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $resultArr[] = $row['seat_number'];
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => "Error during seat fetch"]);
        exit;
    }
}


if (count($resultArr) > 0) {
    echo json_encode(['status' => "success", 'data' => $resultArr]);
    exit;
} else {
    echo json_encode(['status' => 'error', 'message' => "No reserved seat fetched"]);
    exit;
}
