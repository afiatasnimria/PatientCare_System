<?php
header('Content-Type: application/json');
session_start();
require_once __DIR__ . '/db.php';

if (empty($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

$user_id = (int) $_SESSION['user_id'];

if (empty($_FILES['photo']) || $_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'No file uploaded or upload error']);
    exit;
}

$file = $_FILES['photo'];
$allowed = ['image/jpeg','image/png','image/webp'];
if (!in_array($file['type'], $allowed)) {
    echo json_encode(['success' => false, 'message' => 'Unsupported file type']);
    exit;
}

$ext = pathinfo($file['name'], PATHINFO_EXTENSION);
$name = 'profile_' . $user_id . '_' . time() . '.' . $ext;
$destDir = __DIR__ . '/uploads';
if (!is_dir($destDir)) mkdir($destDir, 0755, true);
$dest = $destDir . '/' . $name;

if (!move_uploaded_file($file['tmp_name'], $dest)) {
    echo json_encode(['success' => false, 'message' => 'Failed to move uploaded file']);
    exit;
}

$urlPath = 'uploads/' . $name;

// Ensure user_profiles exists
$conn->query("CREATE TABLE IF NOT EXISTS user_profiles (
    profile_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNIQUE NOT NULL,
    address TEXT,
    house_no VARCHAR(50),
    city VARCHAR(100),
    postal_code VARCHAR(20),
    blood_group VARCHAR(10),
    last_donation DATE,
    donation_count INT,
    emergency_contact VARCHAR(255),
    photo VARCHAR(255),
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
)
");

// Upsert photo path
$check = $conn->prepare('SELECT profile_id FROM user_profiles WHERE user_id = ? LIMIT 1');
$check->bind_param('i', $user_id);
$check->execute();
$check->store_result();
$exists = $check->num_rows > 0;
$check->close();

if ($exists) {
    $stmt = $conn->prepare('UPDATE user_profiles SET photo = ? WHERE user_id = ?');
    $stmt->bind_param('si', $urlPath, $user_id);
    $stmt->execute();
    $stmt->close();
} else {
    $stmt = $conn->prepare('INSERT INTO user_profiles (user_id, photo) VALUES (?, ?)');
    // Note: different binding order to avoid type confusion
    $stmt->bind_param('is', $user_id, $urlPath);
    $stmt->execute();
    $stmt->close();
}

echo json_encode(['success' => true, 'url' => $urlPath]);
exit;
?>
