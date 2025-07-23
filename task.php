<?php
require 'db.php';
$timeline = $_GET['timeline'] ?? '';

if (!$timeline) {
    echo json_encode([]);
    exit;
}

$stmt = $conn->prepare("SELECT * FROM task WHERE timeline = ?");
$stmt->bind_param("s", $timeline);
$stmt->execute();
$result = $stmt->get_result();
echo json_encode($result->fetch_all(MYSQLI_ASSOC));
?>
