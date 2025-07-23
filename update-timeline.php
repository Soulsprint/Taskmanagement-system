<?php
header('Content-Type: application/json');
require 'db.php';

$data = json_decode(file_get_contents('php://input'), true);
if (!isset($data['task_id'], $data['timeline'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing parameters']);
    exit;
}

$taskId = intval($data['task_id']);
$newTimeline = intval($data['timeline']);

$sql = "UPDATE task SET timeline = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $newTimeline, $taskId);

if ($stmt->execute()) {
    echo json_encode(['message' => 'Task reassigned successfully']);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Database update failed']);
}
?>
