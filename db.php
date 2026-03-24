<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "bioelectrodeai";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

/** Provide a singleton-like connection for other scripts. */
function getDB() {
    global $conn;
    return $conn;
}

// Check connection
if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "Connection failed: " . $conn->connect_error]));
}
?>