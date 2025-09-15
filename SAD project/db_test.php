<?php
require_once 'db.php';
header('Content-Type: application/json');
$result = $conn->query("SELECT COUNT(*) AS c FROM users");
if ($result) {
    $row = $result->fetch_assoc();
    echo json_encode(['ok' => true, 'users' => (int)$row['c']]);
} else {
    echo json_encode(['ok' => false, 'error' => $conn->error]);
}
?>