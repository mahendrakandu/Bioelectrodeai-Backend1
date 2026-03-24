<?php
// Test script to check users table structure
$servername = "localhost";
$username = "root";
$password = "";
$database = "bioelectrodeai";

$conn = mysqli_connect($servername, $username, $password, $database);

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Check if users table exists
$result = mysqli_query($conn, "SHOW TABLES LIKE 'users'");
if (mysqli_num_rows($result) == 0) {
    echo "ERROR: users table does not exist!\n";
    echo "Creating users table...\n";
    
    $create_table = "CREATE TABLE users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        email VARCHAR(255) UNIQUE NOT NULL,
        role VARCHAR(255) NOT NULL,
        password VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    if (mysqli_query($conn, $create_table)) {
        echo "SUCCESS: users table created!\n";
    } else {
        echo "ERROR creating table: " . mysqli_error($conn) . "\n";
    }
} else {
    echo "SUCCESS: users table exists\n";
    
    // Show table structure
    $result = mysqli_query($conn, "DESCRIBE users");
    echo "\nTable structure:\n";
    while ($row = mysqli_fetch_assoc($result)) {
        echo "{$row['Field']} - {$row['Type']} - {$row['Null']} - {$row['Key']}\n";
    }
}

mysqli_close($conn);
?>
