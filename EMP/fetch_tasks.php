<?php
$servername = "localhost";
$username = "root";
$password = "Pass@123"; // Default for XAMPP
$database = "domain";  // ✅ Replace with your DB name

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "Connection failed"]));
}

$timeline = isset($_GET['timeline']) ? intval($_GET['timeline']) : 1;

$sql = "SELECT * FROM task WHERE timeline = $timeline";
$result = $conn->query($sql);

$tasks = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $tasks[] = $row;
    }
}

echo json_encode($tasks);
$conn->close();
?>
