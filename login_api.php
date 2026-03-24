<?php
header("Content-Type: application/json");
require_once __DIR__ . "/db.php";

// Read JSON input
$data = json_decode(file_get_contents("php://input"), true);

$email = $data['email'] ?? '';
$password = $data['password'] ?? '';

// Validate input
if (empty($email) || empty($password)) {
    echo json_encode([
        "status" => "error",
        "message" => "Email and Password are required"
    ]);
    exit;
}

// Prepare query
$stmt = $conn->prepare("SELECT id, name, role, password FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

// Check user
if ($result->num_rows === 0) {
    echo json_encode([
        "status" => "error",
        "message" => "Account not found with this email"
    ]);
    exit;
}

$user = $result->fetch_assoc();

// Verify password
if (!password_verify($password, $user['password'])) {
    echo json_encode([
        "status" => "error",
        "message" => "Incorrect password. Please try again."
    ]);
    exit;
}

// Success response
echo json_encode([
    "status" => "success",
    "message" => "Login successful",
    "user" => [
        "id" => $user['id'],
        "name" => $user['name'],
        "email" => $email,
        "role" => $user['role']
    ]
]);
?>
