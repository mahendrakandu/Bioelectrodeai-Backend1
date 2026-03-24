<?php
header("Content-Type: application/json");
require_once __DIR__ . "/db.php";

$id = $_POST['id'] ?? '';
$name = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';
$role = $_POST['role'] ?? '';
$bio = $_POST['bio'] ?? '';

if (empty($id) || empty($email)) {
    echo json_encode([
        "status" => "error",
        "message" => "ID and Email are required"
    ]);
    exit;
}

// Handle Photo Upload
$photoPath = null;
if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = __DIR__ . "/uploads/";
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    $fileInfo = pathinfo($_FILES['photo']['name']);
    $extension = strtolower($fileInfo['extension']);
    $allowedKeys = array('jpg', 'jpeg', 'png', 'gif');
    
    if (in_array($extension, $allowedKeys)) {
        $fileName = "profile_" . $id . "_" . time() . "." . $extension;
        $targetFile = $uploadDir . $fileName;
        
        if (move_uploaded_file($_FILES['photo']['tmp_name'], $targetFile)) {
            $photoPath = "uploads/" . $fileName; // Path to store in DB
        }
    }
}

// Update Database
$updateQuery = "UPDATE users SET name = ?, email = ?, role = ?, bio = ?";
$params = [$name, $email, $role, $bio];
$types = "ssss";

if ($photoPath !== null) {
    // Also update photo
    $updateQuery .= ", profile_photo = ?";
    $params[] = $photoPath;
    $types .= "s";
}

$updateQuery .= " WHERE id = ?";
$params[] = $id;
$types .= "i";

$stmt = $conn->prepare($updateQuery);
$stmt->bind_param($types, ...$params);

if ($stmt->execute()) {
    echo json_encode([
        "status" => "success",
        "message" => "Profile updated successfully!",
        "photo_url" => $photoPath !== null ? $photoPath : ""
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Failed to update profile: " . $stmt->error
    ]);
}
?>
