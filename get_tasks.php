<?php
$host = "localhost";
$user = "root";
$pass = "Pass@1233";
$db = "domain"; // ✅ Replace with your DB name

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "DB connection failed."]));
}

// 💡 Optionally, filter by employee from session or query param
$employee = "Raj"; // Example employee — replace with session or URL parameter if needed

$sql = "SELECT * FROM task WHERE assigned_to = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $employee);
$stmt->execute();
$result = $stmt->get_result();

$tasks = [];
while ($row = $result->fetch_assoc()) {
    $tasks[] = $row;
}

echo json_encode($tasks);
$conn->close();
?>
