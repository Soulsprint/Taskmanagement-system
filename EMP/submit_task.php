<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

$servername = "localhost";
$username = "root";
$password = "Pass@123";
$database = "domain";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "Connection failed"]));
}

$data = json_decode(file_get_contents("php://input"), true);
$taskId = $data['task_id'] ?? $_GET['id'];  // expected field name is task_id

if (!$taskId) {
    echo json_encode(["success" => false, "message" => "Missing task ID"]);
    exit();
}

// ✅ Step 1: Fetch the task from rejected_tasks
$select_sql = "SELECT * FROM rejected_tasks WHERE task_id = ?";
$stmt = $conn->prepare($select_sql);
$stmt->bind_param("i", $taskId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["success" => false, "message" => "Task not found in rejected_tasks"]);
    exit();
}

$task = $result->fetch_assoc();

// ✅ Step 2: Insert into completed_tasks
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
    // ✅ Step 3: Delete from rejected_tasks
    $delete_sql = "DELETE FROM rejected_tasks WHERE task_id = ?";
    $delete_stmt = $conn->prepare($delete_sql);
    $delete_stmt->bind_param("i", $taskId);
    $delete_stmt->execute();

    echo json_encode(["success" => true, "message" => "Task submitted to admin"]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to insert into completed_tasks"]);
}

$conn->close();
?>
