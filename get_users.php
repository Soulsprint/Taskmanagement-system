<?php
include 'db.php';

$sql = "SELECT id, username, role, full_name, created_at, last_login, is_active FROM auth_users ORDER BY role, username";
$result = $conn->query($sql);

$users = [];
while ($row = $result->fetch_assoc()) {
    $users[] = $row;
}

echo json_encode(["users" => $users, "total" => count($users)]);
?>
