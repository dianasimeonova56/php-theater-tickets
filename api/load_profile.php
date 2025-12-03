<?php
require_once "../db.php";
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

$stmt = $conn->prepare(
   "SELECT u.*, 
   b.id as booking_id, 
   b.play_id, 
   p.name as play_name, 
   p.date as play_date, 
   p.image as play_image, 
   (SELECT COUNT(*) FROM tickets t WHERE t.booking_id = b.id) as tickets_count,
   (SELECT SUM(t.price) FROM tickets t WHERE t.booking_id = b.id) AS total_price
    FROM users u
    LEFT JOIN bookings b ON u.id = b.user_id
    LEFT JOIN plays p ON b.play_id = p.id
    WHERE u.id = ?"
);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$resultUser = $stmt->get_result();
$user;
$bookings = [];
while ($row = $resultUser->fetch_assoc()) {
   $user = [
      "email" => $row['email'],
      "first_name" => $row['first_name'],
      "last_name" => $row['last_name'],
      "phone" => $row['phone'],
      "username" => $row['username']
   ];

   $bookings[] =
      [
         "booking_id" => $row["booking_id"],
         "play_id" => $row["play_id"],
         "play_name" => $row["play_name"],
         "play_date" => $row["play_date"],
         "play_image" => $row["play_image"],
         "tickets_count" => $row["tickets_count"],
         "total_price" => $row["total_price"]
      ];
}

echo json_encode(["user" => $user, "bookings" => $bookings]);


