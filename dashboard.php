<?php
header('Content-Type: application/json');
$host = 'localhost';
$user = 'root';
$pass = 'Pass@123';
$db   = 'domain';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

// Count tasks from each table
$result = [
    'tasks' => 0,
    'approved' => 0,
    'rejected' => 0
];

$q1 = $conn->query("SELECT COUNT(*) AS count FROM task");
$q2 = $conn->query("SELECT COUNT(*) AS count FROM approved_tasks");
$q3 = $conn->query("SELECT COUNT(*) AS count FROM rejected_tasks");

if ($q1 && $q2 && $q3) {
    $result['tasks'] = $q1->fetch_assoc()['count'];
    $result['approved'] = $q2->fetch_assoc()['count'];
    $result['rejected'] = $q3->fetch_assoc()['count'];
}

echo json_encode($result);
$conn->close();
?>
