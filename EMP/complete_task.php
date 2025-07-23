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

$taskId = $data['taskId'] ?? $_GET['id']; // fallback support for GET too

// Step 1: Fetch the task
$select_sql = "SELECT * FROM task WHERE id = ?";
$stmt = $conn->prepare($select_sql);
$stmt->bind_param("i", $taskId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["success" => false, "message" => "Task not found"]);
    exit();
}

$task = $result->fetch_assoc();

// Step 2: Insert into completed_tasks (only valid columns)
$insert_sql = "INSERT INTO completed_tasks 
    (name, number, accessories, problem_reported, timeline, assigned_to)
    VALUES (?, ?, ?, ?, ?, ?)";

$insert_stmt = $conn->prepare($insert_sql);
$insert_stmt->bind_param(
    "ssssis",
    $task['name'],
    $task['number'],
    $task['accessories'],
    $task['problem_reported'],
    $task['timeline'],
    $task['assigned_to']
);

if ($insert_stmt->execute()) {
    // Step 3: Delete the original task
    $delete_sql = "DELETE FROM task WHERE id = ?";
    $delete_stmt = $conn->prepare($delete_sql);
    $delete_stmt->bind_param("i", $taskId);
    $delete_stmt->execute();

    echo json_encode(["success" => true, "message" => "Task submitted to admin"]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to insert into completed_tasks"]);
}

$conn->close();
?>
