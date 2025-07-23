<?php
include 'db.php';

$data = json_decode(file_get_contents("php://input"), true);

$username = $data['username'];
$password = hash('sha256', $data['password']);

$sql = "SELECT id, username, role, full_name, is_active FROM auth_users WHERE username=? AND password=? AND is_active=1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $username, $password);
$stmt->execute();

$result = $stmt->get_result();
if ($result->num_rows === 0) {
    echo json_encode(["error" => "Invalid username or password"]);
    http_response_code(401);
} else {
    $user = $result->fetch_assoc();

    // update last login
    $update = $conn->prepare("UPDATE auth_users SET last_login=NOW() WHERE id=?");
    $update->bind_param("i", $user['id']);
    $update->execute();

    echo json_encode([
        "message" => "Login successful",
        "user" => $user
    ]);
}
?>
