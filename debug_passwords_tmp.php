<?php
require_once "db.php";
$stmt = $conn->query("SELECT email, password FROM users LIMIT 10");
while($r = $stmt->fetch_assoc()) {
    echo "Email: " . $r['email'] . "\n";
    echo "Hash: " . substr($r['password'], 0, 10) . "...\n";
    echo "Is Valid Hash: " . (password_get_info($r['password'])['algoName'] !== 'unknown' ? 'YES' : 'NO') . "\n\n";
}
?>
