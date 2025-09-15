<?php
header('Content-Type: application/json');
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
require_once 'db.php';

$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

// Map incoming fields: the frontend sends 'username' but DB uses 'email'
$role = isset($input['role']) ? $input['role'] : '';
$username = isset($input['username']) ? $input['username'] : '';
$password = isset($input['password']) ? $input['password'] : '';

if (!$role || !$username || !$password) {
    echo json_encode(['success' => false, 'message' => 'Missing credentials']);
    exit;
}

// Use prepared statement against `email` and `role` columns (DB schema uses email/password)
$stmt = $conn->prepare('SELECT user_id, full_name, email, password, role FROM users WHERE email = ? AND role = ? LIMIT 1');
if (!$stmt) {
    $err = $conn->error;
    error_log("authenticate.php prepare error: $err\n", 3, __DIR__ . '/auth_debug.log');
    echo json_encode(['success' => false, 'message' => 'Database prepare error', 'debug' => $err]);
    exit;
}
$stmt->bind_param('ss', $username, $role);
$exec = $stmt->execute();
if (!$exec) {
    $err = $stmt->error;
    error_log("authenticate.php execute error: $err\n", 3, __DIR__ . '/auth_debug.log');
    echo json_encode(['success' => false, 'message' => 'Database execute error', 'debug' => $err]);
    $stmt->close();
    exit;
}
$res = $stmt->get_result();
if ($res->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid email or password']);
    $stmt->close();
    exit;
}

$row = $res->fetch_assoc();
// Passwords are stored in `password` column as password_hash()
if (password_verify($password, $row['password'])) {
    // Authentication successful - set session
    $_SESSION['user_id'] = (int)$row['user_id'];
    $_SESSION['user_name'] = $row['full_name'];
    $_SESSION['user_role'] = $row['role'];

    echo json_encode(['success' => true, 'name' => $row['full_name'], 'user_id' => (int)$row['user_id']]);
    $stmt->close();
    exit;
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid email or password']);
    $stmt->close();
    exit;
}

?>
