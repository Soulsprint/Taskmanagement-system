<?php
include 'db.php';

$data = json_decode(file_get_contents("php://input"), true);
$username = $data['username'];
$old_password = hash('sha256', $data['old_password']);
$new_password = hash('sha256', $data['new_password']);

$check = $conn->prepare("SELECT id FROM auth_users WHERE username=? AND password=?");
$check->bind_param("ss", $username, $old_password);
$check->execute();
$result = $check->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["error" => "Invalid current password"]);
    http_response_code(401);
    exit;
}

$update = $conn->prepare("UPDATE auth_users SET password=? WHERE username=?");
$update->bind_param("ss", $new_password, $username);
$update->execute();

echo json_encode(["message" => "Password changed successfully"]);
?>
