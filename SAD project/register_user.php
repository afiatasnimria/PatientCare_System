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

$role = isset($input['role']) ? $input['role'] : '';
$name = isset($input['name']) ? $input['name'] : '';
$username = isset($input['username']) ? $input['username'] : '';
$password = isset($input['password']) ? $input['password'] : '';
$phone = isset($input['phone']) ? $input['phone'] : '';

if (!$role || !$name || !$username || !$password) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

// Check if email exists
$check = $conn->prepare('SELECT user_id FROM users WHERE email = ? LIMIT 1');
if (!$check) {
    $err = $conn->error;
    error_log("register_user.php prepare (check) error: $err\n", 3, __DIR__ . '/auth_debug.log');
    echo json_encode(['success' => false, 'message' => 'Database prepare error', 'debug' => $err]);
    exit;
}
$check->bind_param('s', $username);
$exec = $check->execute();
if (!$exec) {
    $err = $check->error;
    error_log("register_user.php execute (check) error: $err\n", 3, __DIR__ . '/auth_debug.log');
    echo json_encode(['success' => false, 'message' => 'Database execute error', 'debug' => $err]);
    $check->close();
    exit;
}
$res = $check->get_result();
if ($res && $res->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'Email already exists']);
    $check->close();
    exit;
}
$check->close();

// Hash password and insert into DB (columns: full_name, email, password, phone, role)
$hash = password_hash($password, PASSWORD_DEFAULT);

$stmt = $conn->prepare('INSERT INTO users (full_name, email, password, phone, role) VALUES (?, ?, ?, ?, ?)');
$err = $conn->error;
if (!$stmt) {
    error_log("register_user.php prepare (insert) error: $err\n", 3, __DIR__ . '/auth_debug.log');
    echo json_encode(['success' => false, 'message' => 'Prepare failed', 'debug' => $err]);
    exit;
}
$stmt->bind_param('sssss', $name, $username, $hash, $phone, $role);
if ($stmt->execute()) {
    $newId = $stmt->insert_id;
    // Auto-login: set session
    $_SESSION['user_id'] = (int)$newId;
    $_SESSION['user_name'] = $name;
    $_SESSION['user_role'] = $role;

    echo json_encode(['success' => true, 'user_id' => $newId]);
} else {
    $err = $stmt->error;
    error_log("register_user.php insert execute error: $err\n", 3, __DIR__ . '/auth_debug.log');
    echo json_encode(['success' => false, 'message' => 'Insert failed', 'debug' => $err]);
}

$stmt->close();

?>
