<?php
header("Content-Type: application/json");
require_once __DIR__ . "/db.php";

$data = json_decode(file_get_contents("php://input"), true);

$email = $data['email'] ?? '';
$new_password = $data['new_password'] ?? '';

if (empty($email) || empty($new_password)) {
    echo json_encode(["status" => "error", "message" => "Email and new password are required"]);
    exit;
}

// Check if user exists
$stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    echo json_encode(["status" => "error", "message" => "User not found"]);
    exit;
}

// Update password
$hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
$stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
$stmt->bind_param("ss", $hashed_password, $email);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Password reset successfully"]);
} else {
    echo json_encode(["status" => "error", "message" => "Failed to reset password"]);
}
?>