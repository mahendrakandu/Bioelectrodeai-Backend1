<?php
header("Content-Type: application/json");
require_once __DIR__ . "/db.php";

$data = json_decode(file_get_contents("php://input"), true);

$email = $data['email'] ?? '';

if (empty($email)) {
    echo json_encode(["status" => "error", "message" => "Email is required", "exists" => false]);
    exit;
}

$stmt = $conn->prepare("SELECT name FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    echo json_encode([
        "status" => "success",
        "message" => "Email exists",
        "exists" => true,
        "user_name" => $user['name']
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Email not found",
        "exists" => false
    ]);
}
?>
