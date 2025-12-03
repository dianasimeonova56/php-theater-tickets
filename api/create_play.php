<?php
session_start();
require_once "../db.php";

if (empty($_POST['playName']) || empty($_POST['description']) || empty($_POST['playDate']) || empty($_POST['playDuration']) || empty($_FILES['playImg'])) {
    echo json_encode(['status' => 'error', 'message' => 'Provide info for all fields!']);
    exit;
}

$play_name = trim($_POST['playName']);
$description = trim($_POST['description']);
$play_date = $_POST['playDate'];
$play_duration = filter_var($_POST['playDuration'], FILTER_VALIDATE_INT);
$play_img = $_FILES['playImg']['name'];

if ($play_duration <= 0) {
    echo json_encode(['status'=>'error','message'=>'Invalid play duration!']);
    exit;
}

$play_timestamp = strtotime($play_date);

if ($play_timestamp === false) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid date format!']);
    exit;
}

if (time() > $play_timestamp) {
    echo json_encode(['status' => 'error', 'message' => 'Please choose a future date!']);
    exit;
}


$dir = "../assets/uploads/";
$allowed = ["jpg", "png", "jpeg", "gif", "webp"];
$ext = pathinfo($_FILES["playImg"]['name'], PATHINFO_EXTENSION);
if (!in_array($ext, $allowed)) {
    echo json_encode(['status' => "error", "message" => "Please upload a file with a correct extension [jps, jpeg, png, gif, webp]!"]);
    exit;
}
$filename = $_FILES["playImg"]["name"];
$fullPath = $dir . $filename;

if (move_uploaded_file($_FILES['playImg']['tmp_name'], $fullPath)) {
    $stmt = $conn->prepare("INSERT INTO plays (name, description, date, duration, image) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssis", $play_name, $description, $play_date, $play_duration, $play_img);
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Play added!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error during play add! Maybe check fields?']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Error during file upload!']);
}
