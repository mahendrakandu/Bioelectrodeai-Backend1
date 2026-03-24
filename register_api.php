<?php
header("Content-Type: application/json");
require_once __DIR__ . "/db.php";

$data = json_decode(file_get_contents("php://input"), true);

$fullname = $data['fullname'] ?? '';
$email = $data['email'] ?? '';
$role = $data['role'] ?? '';
$password = $data['password'] ?? '';
$confirm_password = $data['confirm_password'] ?? '';

if (!$conn) {
    echo json_encode(["status" => "error", "message" => "DB not connected"]);
    exit;
}

if (empty($fullname) || empty($email) || empty($role) || empty($password) || empty($confirm_password)) {
    echo json_encode(["status" => "error", "message" => "All fields are required"]);
    exit;
}

if ($password !== $confirm_password) {
    echo json_encode(["status" => "error", "message" => "Passwords do not match"]);
    exit;
}

$stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo json_encode(["status" => "error", "message" => "Email already exists"]);
    exit;
}

$hashed_password = password_hash($password, PASSWORD_DEFAULT);

$stmt = $conn->prepare(
    "INSERT INTO users (name, email, role, password) VALUES (?, ?, ?, ?)"
);
$stmt->bind_param("ssss", $fullname, $email, $role, $hashed_password);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "User registered successfully"]);
} else {
    echo json_encode(["status" => "error", "message" => "Registration failed"]);
}
?>
