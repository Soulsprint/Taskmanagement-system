<?php
header('Content-Type: application/json');
require 'db.php';

$timeline = isset($_GET['timeline']) ? intval($_GET['timeline']) : 0;

$sql = "SELECT * FROM task WHERE timeline = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $timeline);
$stmt->execute();
$result = $stmt->get_result();

$tasks = [];
while ($row = $result->fetch_assoc()) {
    $tasks[] = $row;
}
echo json_encode($tasks);
?>
