<?php
session_start();
require_once "../db.php";

if (empty($_POST["playId"]) || empty($_POST["selectedSeats"])) {
    echo json_encode(['status' => 'error', 'message' => 'Please fill all fields!']);
    exit;
}

$playId = filter_input(INPUT_POST, "playId", FILTER_VALIDATE_INT);
$selectedSeats = json_decode($_POST['selectedSeats'], true);
$userId = $_SESSION['user_id'];

if (!is_array($selectedSeats) || count($selectedSeats) === 0) {
    echo json_encode(['status'=>'error','message'=>'No seats selected!']);
    exit;
}

$stmt = $conn->prepare("INSERT INTO bookings (play_id,user_id) VALUES (?, ?)");
$stmt->bind_param("ii", $playId, $userId);
if ($stmt->execute()) {
    $bookingId = $stmt->insert_id;

    $check_seat = $conn->prepare("SELECT * FROM tickets t JOIN bookings b on t.booking_id = b.id WHERE b.play_id = ?");
    $check_seat->bind_param("i", $playId);
    $check_seat->execute();
    $result = $check_seat->get_result();

    $takenSeats = [];
    while ($row = $result->fetch_assoc()) {
        $takenSeats[] = $row['seat_number'];
    }

    // for each ticket -> insert into table
    foreach ($selectedSeats as $ticket) {
        $seat = $ticket['id'];
        if (!preg_match("/^[0-9]+_[0-9]+$/", $seat)) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid seat format']);
            exit;
        }
        $price = $ticket['price'];

        if (in_array($seat, $takenSeats)) {
            echo json_encode(['status' => "error", "message" => "Seat " . $seat . " is already booked!"]);
            exit;
        }

        $insert_ticket = $conn->prepare("INSERT INTO tickets (seat_number, price, booking_id) VALUES (?, ?, ?)");
        $insert_ticket->bind_param("sii", $seat, $price, $bookingId);
        if (!$insert_ticket->execute()) {
            echo json_encode(['status' => "error", 'message' => 'Error during ticket creation!']);
            exit;
        }
    }

    echo json_encode(['status' => 'success', 'message' => 'Booking completed!']);
    exit;
} else {
    echo json_encode(['status' => 'error', 'message' => "Error during booking!"]);
    exit;
}
