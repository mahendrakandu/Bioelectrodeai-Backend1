<?php
include "db.php";
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Ensure to handle Preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Database Connection
$host = "localhost";
$db_name = "bioelectrodeai"; 
$username = "root";
$password = "";

try {
    $conn = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $exception) {
    echo json_encode(["status" => "error", "message" => "Connection error: " . $exception->getMessage()]);
    exit();
}

$data = json_decode(file_get_contents("php://input"));

if (!isset($data->action)) {
    echo json_encode(["status" => "error", "message" => "Action not specified."]);
    exit();
}

switch($data->action) {
    case 'login':
        // Optional Admin verify if you want separate login entry
        $email = $data->email ?? '';
        $pass = $data->password ?? '';
        
        $query = "SELECT * FROM users WHERE email = :email LIMIT 1";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(":email", $email);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Auto-create default admin on first login attempt if it doesn't exist
        if (!$user && $email === 'admin@bioelectrode.ai') {
            $hashed = password_hash($pass, PASSWORD_BCRYPT);
            $insertQuery = "INSERT INTO users (name, email, password, role, status) VALUES ('Super Admin', 'admin@bioelectrode.ai', :hash, 'Admin', 'Active')";
            $insertStmt = $conn->prepare($insertQuery);
            $insertStmt->bindParam(":hash", $hashed);
            $insertStmt->execute();
            
            // Fetch it again
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
        }

        if ($user && password_verify($pass, $user['password'])) {
            if ($user['role'] === 'Admin') {
                // Return Success Admin
                echo json_encode([
                    "status" => "success",
                    "message" => "Admin login successful",
                    "user" => [
                        "id" => $user['id'],
                        "name" => $user['name'],
                        "email" => $user['email'],
                        "role" => $user['role'],
                        "status" => $user['status']
                    ]
                ]);
            } else {
                echo json_encode(["status" => "error", "message" => "Access denied. Insufficient privileges."]);
            }
        } else {
            echo json_encode(["status" => "error", "message" => "Invalid email or password."]);
        }
        break;

    case 'get_users':
        $query = "SELECT id, name, email, role, status, created_at, last_login, bio FROM users ORDER BY created_at DESC";
        $stmt = $conn->prepare($query);
        $stmt->execute();
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode([
            "status" => "success",
            "users" => $users
        ]);
        break;

    case 'update_user':
        $userId = $data->user_id ?? '';
        $role = $data->role ?? '';
        $status = $data->status ?? '';
        
        if(empty($userId)) {
            echo json_encode(["status" => "error", "message" => "User ID is required."]);
            exit();
        }

        $query = "UPDATE users SET role = :role, status = :status WHERE id = :id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(":role", $role);
        $stmt->bindParam(":status", $status);
        $stmt->bindParam(":id", $userId);
        
        if($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "User updated successfully."]);
        } else {
            echo json_encode(["status" => "error", "message" => "Failed to update user."]);
        }
        break;

    case 'admin_register':
        $name = $data->name ?? '';
        $email = $data->email ?? '';
        $password = $data->password ?? ''; 
        $role = 'Admin';
        $status = 'Active';

        if(empty($name) || empty($email) || empty($password)) {
            echo json_encode(["status" => "error", "message" => "Name, Email, and Password required."]);
            exit();
        }

        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        $query = "INSERT INTO users (name, email, password, role, status) VALUES (:name, :email, :password, :role, :status)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(":name", $name);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":password", $hashed_password);
        $stmt->bindParam(":role", $role);
        $stmt->bindParam(":status", $status);
        
        try {
            if($stmt->execute()) {
                echo json_encode(["status" => "success", "message" => "Admin account created successfully."]);
            } else {
                echo json_encode(["status" => "error", "message" => "Failed to register admin."]);
            }
        } catch(PDOException $e) {
            echo json_encode(["status" => "error", "message" => "Email already exists."]);
        }
        break;

    case 'add_user':
        $name = $data->name ?? '';
        $email = $data->email ?? '';
        $password = $data->password ?? ''; // In real app, this should be sent hashed or hashed here
        $role = $data->role ?? 'Student';
        $status = $data->status ?? 'Active'; // Automatically active if added by admin

        if(empty($name) || empty($email) || empty($password)) {
            echo json_encode(["status" => "error", "message" => "Name, Email, and Password required."]);
            exit();
        }

        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        $query = "INSERT INTO users (name, email, password, role, status) VALUES (:name, :email, :password, :role, :status)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(":name", $name);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":password", $hashed_password);
        $stmt->bindParam(":role", $role);
        $stmt->bindParam(":status", $status);
        
        try {
            if($stmt->execute()) {
                echo json_encode(["status" => "success", "message" => "User created successfully."]);
            } else {
                echo json_encode(["status" => "error", "message" => "Failed to add user."]);
            }
        } catch(PDOException $e) {
            echo json_encode(["status" => "error", "message" => "Error, email likely already exists."]);
        }
        break;

    case 'delete_user':
        $userId = $data->user_id ?? '';
        
        if(empty($userId)) {
            echo json_encode(["status" => "error", "message" => "User ID is required."]);
            exit();
        }

        $query = "DELETE FROM users WHERE id = :id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(":id", $userId);
        
        if($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "User deleted successfully."]);
        } else {
            echo json_encode(["status" => "error", "message" => "Failed to delete user."]);
        }
        break;

    case 'get_app_items':
        $query = "SELECT id, title, description, type, added_date FROM app_items ORDER BY added_date DESC";
        $stmt = $conn->prepare($query);
        $stmt->execute();
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode([
            "status" => "success",
            "items" => $items
        ]);
        break;

    case 'add_app_item':
        $title = $data->title ?? '';
        $description = $data->description ?? '';
        $type = $data->type ?? 'Announcement';
        
        if(empty($title) || empty($description)) {
            echo json_encode(["status" => "error", "message" => "Title and Description required."]);
            exit();
        }

        $query = "INSERT INTO app_items (title, description, type) VALUES (:title, :description, :type)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(":title", $title);
        $stmt->bindParam(":description", $description);
        $stmt->bindParam(":type", $type);
        
        if($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "App Item added."]);
        } else {
            echo json_encode(["status" => "error", "message" => "Failed to add item."]);
        }
        break;

    case 'get_stats':
        // Fast counting queries for dashboard
        $stats = [];
        
        // Total Users
        $stmt = $conn->query("SELECT COUNT(*) FROM users");
        $stats['total_users'] = $stmt->fetchColumn();
        
        // Students
        $stmt = $conn->query("SELECT COUNT(*) FROM users WHERE role = 'Student'");
        $stats['student_count'] = $stmt->fetchColumn();
        
        // Datasets
        $stmt = $conn->query("SELECT COUNT(*) FROM datasets");
        $stats['total_datasets'] = $stmt->fetchColumn();
        
        // Most recent model
        $stmt = $conn->query("SELECT * FROM ai_models ORDER BY last_trained DESC LIMIT 1");
        $stats['latest_model'] = $stmt->fetch(PDO::FETCH_ASSOC);

        echo json_encode([
            "status" => "success",
            "stats" => $stats
        ]);
        break;

    case 'get_user_progress':
        $userId = $data->user_id ?? '';
        if(empty($userId)) {
            echo json_encode(["status" => "error", "message" => "User ID is required."]);
            exit();
        }

        // 1. Create progress table if not exists
        $createTableQuery = "CREATE TABLE IF NOT EXISTS `user_progress` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `user_id` INT,
            `module_name` VARCHAR(255) NOT NULL,
            `progress_percentage` INT DEFAULT 0,
            `score` INT DEFAULT 0,
            `last_accessed` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
        )";
        $conn->exec($createTableQuery);

        // 2. Check if user has progress
        $checkQuery = "SELECT * FROM `user_progress` WHERE `user_id` = :uid";
        $stmt = $conn->prepare($checkQuery);
        $stmt->bindParam(":uid", $userId);
        $stmt->execute();
        $progress = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 3. If no progress found, seed some demo data for this user to demonstrate the feature
        if (empty($progress)) {
            $seedQuery = "INSERT INTO `user_progress` (`user_id`, `module_name`, `progress_percentage`, `score`) VALUES 
                (:uid, 'Introduction to Biosignals', 100, 95),
                (:uid, 'Bipolar vs Monopolar Setup', 80, 85),
                (:uid, 'Signal Artifacts & Filtering', 40, 0),
                (:uid, 'Advanced AI Analysis', 0, 0)";
            $seedStmt = $conn->prepare($seedQuery);
            $seedStmt->bindParam(":uid", $userId);
            $seedStmt->execute();

            // Re-fetch
            $stmt->execute();
            $progress = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        // 4. Calculate overall stats
        $totalModules = count($progress);
        $completedModules = 0;
        $totalScore = 0;
        $scoredModules = 0;
        $overallProgress = 0;

        foreach ($progress as $p) {
            $overallProgress += $p['progress_percentage'];
            if ($p['progress_percentage'] == 100) {
                $completedModules++;
            }
            if ($p['score'] > 0) {
                $totalScore += $p['score'];
                $scoredModules++;
            }
        }

        $averageScore = $scoredModules > 0 ? round($totalScore / $scoredModules) : 0;
        $averageProgress = $totalModules > 0 ? round($overallProgress / $totalModules) : 0;

        echo json_encode([
            "status" => "success",
            "overall_progress" => $averageProgress,
            "completed_modules" => $completedModules,
            "total_modules" => $totalModules,
            "average_score" => $averageScore,
            "modules" => $progress
        ]);
        break;
        
    default:
        echo json_encode(["status" => "error", "message" => "Invalid action."]);
        break;
}
?>
