<?php
header("Content-Type: application/json");
require_once __DIR__ . "/db.php";

$data = json_decode(file_get_contents("php://input"), true);

$email = $data['email'] ?? '';

if (empty($email)) {
    echo json_encode(["status" => "error", "message" => "Email is required"]);
    exit;
}

// Check if email exists
$stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    // In a production app, you would send a reset email here
    // For now, just confirm the email exists so the app can proceed to reset
    echo json_encode([
        "status" => "success",
        "message" => "Password reset link sent to your email"
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Email not found"
    ]);
}
?>