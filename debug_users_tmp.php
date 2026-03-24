<?php
require_once "db.php";
$res = $conn->query("SELECT id, name, email, role, status FROM users");
while($r = $res->fetch_assoc()) {
    print_r($r);
}
?>
