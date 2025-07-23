<?php
$servername = "localhost";
$username = "root";
$password = "Pass@123";
$database = "domain";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "Connection failed"]));
}

$data = json_decode(file_get_contents("php://input"), true);
$taskId = $data['taskId'];
$status = $data['status'] ?? null;
$response = $data['response'] ?? null;

$sql = "UPDATE task SET status = ?, response = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssi", $status, $response, $taskId);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Task updated"]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to update"]);
}

$conn->close();
?>
