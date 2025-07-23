<?php
$host = "localhost"; // or 127.0.0.1
$user = "u270167502_taskmanage";
$pass = "9Pd;DOrk>";
$db   = "u270167502_taskmanage";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "Connection failed."]));
}

$method = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

if ($method === "GET" && preg_match("/completed-tasks\/(\d+)/", $uri, $matches)) {
    $timeline = intval($matches[1]);
    $stmt = $conn->prepare("SELECT * FROM completed_tasks WHERE timeline = ?");
    $stmt->bind_param("i", $timeline);
    $stmt->execute();
    $result = $stmt->get_result();
    $tasks = $result->fetch_all(MYSQLI_ASSOC);
    echo json_encode($tasks);

} elseif ($method === "POST" && preg_match("/approve-task\/(\d+)/", $uri, $matches)) {
    $task_id = intval($matches[1]);

    $task = $conn->query("SELECT * FROM completed_tasks WHERE id = $task_id")->fetch_assoc();

    if ($task) {
        $stmt = $conn->prepare("INSERT INTO approved_tasks (name, number, accessories, problem_reported, timeline) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssi", $task['name'], $task['number'], $task['accessories'], $task['problem_reported'], $task['timeline']);
        
        if ($stmt->execute()) {
            // ✅ Now delete from completed_tasks
            $conn->query("DELETE FROM completed_tasks WHERE id = $task_id");
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "message" => "Insert failed."]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Task not found."]);
    }

} elseif ($method === "POST" && preg_match("/reject-task\/(\d+)/", $uri, $matches)) {
    $task_id = intval($matches[1]);
    $data = json_decode(file_get_contents("php://input"), true);
    $note = $data["note"] ?? '';
    // $assigned_to = $data["assigned_to"] ?? '';
    // Manually assign to 888
    $assigned_to = "888";

    $task = $conn->query("SELECT * FROM completed_tasks WHERE id = $task_id")->fetch_assoc();

    if ($task) {
        $stmt = $conn->prepare("INSERT INTO rejected_tasks (task_id, name, number, accessories, problem_reported, timeline, assigned_to, rejection_note) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param(
            "issssiss",
            $task_id,
            $task['name'],
            $task['number'],
            $task['accessories'],
            $task['problem_reported'],
            $task['timeline'],
            $assigned_to,
            $note
        );

        if ($stmt->execute()) {
            // ✅ Delete from completed_tasks after successful rejection
            $conn->query("DELETE FROM completed_tasks WHERE id = $task_id");
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "message" => "Insert into rejected_tasks failed."]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Task not found."]);
    }

} else {
    echo json_encode(["success" => false, "message" => "Invalid request."]);
}
?>
