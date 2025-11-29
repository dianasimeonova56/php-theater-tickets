<?php
session_start();
require_once "/db.php";

if (empty($_POST['playId']) || empty($_POST['playName']) || empty($_POST['description']) || empty($_POST['playDate']) || empty($_POST['playDuration']) || empty($_FILES['playImg'])) {
    echo json_encode(['status' => 'error', 'message' => 'Provide info for all fields!']);
    exit;
}

$play_id = $_POST['playId'];
$play_name = $_POST['playName'];
$description = $_POST['description'];
$play_date = $_POST['playDate'];
$play_duration = $_POST['playDuration'];
$play_img = $_FILES['playImg']['name'] ?? "";

if ($play_img != "") {
    $dir = "../assets/uploads/";
    $allowed = ["jpg", "png", "jpeg", "gif", "webp"];
    $ext = pathinfo($_FILES["playImg"]['name'], PATHINFO_EXTENSION);
    if (!in_array($ext, $allowed)) {
        echo json_encode(['status' => "error", "message" => "Please upload a file with a correct extension [jps, jpeg, png, gif, webp]!"]);
        exit;
    }

    $filename = uniqid() . $_FILES["playImg"]["name"] . "." . $ext;
    $fullPath = $dir . $filename;

    if (move_uploaded_file($_FILES['playImg']['tmp_name'], $fullPath)) {
        $stmt = $conn->prepare("UPDATE plays SET name = ?, description = ?, date = ?, duration = ?, image = ? WHERE id = ?");
        $stmt->bind_param("sssisi", $play_name, $description, $play_date, $play_duration, $play_img, $play_id);
        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Play updated!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error during play edit! Maybe check fields?']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error during file upload!']);
    }
} else {
    $stmt = $conn->prepare("UPDATE plays SET name = ?, description = ?, date = ?, duration = ? WHERE id = ?");
    $stmt->bind_param("sssis", $play_name, $description, $play_date, $play_duration, $play_id);
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Play updated!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error during play edit! Maybe check fields?']);
    }
}
