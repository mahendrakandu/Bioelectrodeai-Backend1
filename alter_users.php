<?php
// Script to add bio and profile_photo columns
$servername = "localhost";
$username = "root";
$password = "";
$database = "bioelectrodeai";

$conn = mysqli_connect($servername, $username, $password, $database);

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

$alter_sql = "ALTER TABLE users ADD COLUMN bio TEXT NULL, ADD COLUMN profile_photo VARCHAR(255) NULL;";

if (mysqli_query($conn, $alter_sql)) {
    echo "SUCCESS: Added bio and profile_photo columns to users table.\n";
} else {
    echo "ERROR (might already exist): " . mysqli_error($conn) . "\n";
}

mysqli_close($conn);
?>
