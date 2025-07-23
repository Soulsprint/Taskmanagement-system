<?php
$host = "localhost"; // or 127.0.0.1
$user = "root";
$pass = "Pass@123";
$db   = "domain";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
