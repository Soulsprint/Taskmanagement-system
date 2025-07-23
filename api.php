<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");  // Allow CORS for local testing
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");

$host = "localhost"; // or 127.0.0.1
$user = "root";
$pass = "Pass@123";
$db   = "domain";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$method = $_SERVER['REQUEST_METHOD'];
$request = explode("/", trim($_SERVER["REQUEST_URI"], "/"));
$endpoint = end($request);

// Allow preflight for CORS
if ($method === "OPTIONS") {
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);

// 1. Store Task
if ($method === 'POST' && $endpoint === 'store-task') {
    if (!isset($data['name'], $data['number'], $data['accessories'], $data['problem_reported'], $data['timeline'])) {
        http_response_code(400);
        echo json_encode(["error" => "Missing fields"]);
        exit();
    }

    $stmt = $conn->prepare("INSERT INTO storage (name, number, accessories, problem_reported, timeline, status) VALUES (?, ?, ?, ?, ?, 'stored')");
    $stmt->bind_param("ssssi", $data['name'], $data['number'], $data['accessories'], $data['problem_reported'], $data['timeline']);

    if ($stmt->execute()) {
        echo json_encode(["message" => "✅ Task stored successfully", "insertId" => $stmt->insert_id]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "❌ DB Insert Failed", "details" => $stmt->error]);
    }
    $stmt->close();

// 2. Save Task
} elseif ($method === 'POST' && $endpoint === 'save-task') {
    if (!isset($data['name'], $data['number'], $data['accessories'], $data['problem_reported'], $data['timeline'], $data['assigned_to'])) {
        http_response_code(400);
        echo json_encode(["error" => "Missing fields"]);
        exit();
    }

    $stmt = $conn->prepare("INSERT INTO task (name, number, accessories, problem_reported, timeline, assigned_to) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssii", $data['name'], $data['number'], $data['accessories'], $data['problem_reported'], $data['timeline'], $data['assigned_to']);

    if ($stmt->execute()) {
        echo json_encode(["message" => "✅ Task saved", "insertId" => $stmt->insert_id]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "❌ DB Save Failed", "details" => $stmt->error]);
    }
    $stmt->close();

// 3. Delete stored task
} elseif ($method === 'POST' && $endpoint === 'delete-stored-task') {
    if (!isset($data['id'])) {
        http_response_code(400);
        echo json_encode(["error" => "Missing ID"]);
        exit();
    }

    $stmt = $conn->prepare("DELETE FROM storage WHERE id = ?");
    $stmt->bind_param("i", $data['id']);

    if ($stmt->execute()) {
        echo json_encode(["message" => "🗑 Task deleted from storage"]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "❌ Deletion failed", "details" => $stmt->error]);
    }
    $stmt->close();

// 4. Fetch stored tasks
} elseif ($method === 'GET' && $endpoint === 'storage-tasks') {
    $result = $conn->query("SELECT * FROM storage");
    $tasks = [];

    while ($row = $result->fetch_assoc()) {
        $tasks[] = $row;
    }

    echo json_encode($tasks);
} else {
    http_response_code(404);
    echo json_encode(["error" => "Invalid endpoint"]);
}

$conn->close();
