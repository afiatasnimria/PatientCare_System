<?php
header('Content-Type: application/json');
session_start();
require_once __DIR__ . '/db.php';

if (empty($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit;
}

$user_id = (int) $_SESSION['user_id'];

// Basic fields we'll update in users table
$full_name = isset($input['full_name']) ? trim($input['full_name']) : null;
$email = isset($input['email']) ? trim($input['email']) : null;
$phone = isset($input['phone']) ? trim($input['phone']) : null;

// Extended profile fields
$address = isset($input['address']) ? trim($input['address']) : null;
$house_no = isset($input['house_no']) ? trim($input['house_no']) : null;
$city = isset($input['city']) ? trim($input['city']) : null;
$postal_code = isset($input['postal_code']) ? trim($input['postal_code']) : null;
$blood_group = isset($input['blood_group']) ? trim($input['blood_group']) : null;
$last_donation = isset($input['last_donation']) ? trim($input['last_donation']) : null;
$donation_count = isset($input['donation_count']) ? (int)$input['donation_count'] : null;
$emergency_contact = isset($input['emergency_contact']) ? trim($input['emergency_contact']) : null;
$photo = isset($input['photo']) ? trim($input['photo']) : null;

// Ensure user_profiles table exists (simple migration)
$createProfiles = "CREATE TABLE IF NOT EXISTS user_profiles (
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
    photo LONGTEXT,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
)
";
$conn->query($createProfiles);

// Update users table
if ($full_name || $email || $phone) {
    $parts = [];
    $types = '';
    $values = [];
    if ($full_name) { $parts[] = 'full_name = ?'; $types .= 's'; $values[] = $full_name; }
    if ($email) { $parts[] = 'email = ?'; $types .= 's'; $values[] = $email; }
    if ($phone) { $parts[] = 'phone = ?'; $types .= 's'; $values[] = $phone; }

    if (!empty($parts)) {
        $sql = 'UPDATE users SET ' . implode(', ', $parts) . ' WHERE user_id = ? LIMIT 1';
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            echo json_encode(['success' => false, 'message' => 'Prepare failed: ' . $conn->error]);
            exit;
        }
        $types .= 'i';
        $values[] = $user_id;
        $stmt->bind_param($types, ...$values);
        if (!$stmt->execute()) {
            echo json_encode(['success' => false, 'message' => 'Execute failed: ' . $stmt->error]);
            exit;
        }
        $stmt->close();
    }
}

// Upsert into user_profiles
$check = $conn->prepare('SELECT profile_id FROM user_profiles WHERE user_id = ? LIMIT 1');
$check->bind_param('i', $user_id);
$check->execute();
$check->store_result();
$exists = $check->num_rows > 0;
$check->close();

if ($exists) {
    $sql = 'UPDATE user_profiles SET address=?, house_no=?, city=?, postal_code=?, blood_group=?, last_donation=?, donation_count=?, emergency_contact=? WHERE user_id=? LIMIT 1';
    $stmt = $conn->prepare($sql);
    if (!$stmt) { echo json_encode(['success'=>false,'message'=>'Prepare failed: '.$conn->error]); exit; }
    $stmt->bind_param('ssssssisi', $address, $house_no, $city, $postal_code, $blood_group, $last_donation, $donation_count, $emergency_contact, $user_id);
    if (!$stmt->execute()) { echo json_encode(['success'=>false,'message'=>'Execute failed: '.$stmt->error]); exit; }
    $stmt->close();
} else {
    $sql = 'INSERT INTO user_profiles (user_id, address, house_no, city, postal_code, blood_group, last_donation, donation_count, emergency_contact) VALUES (?,?,?,?,?,?,?,?,?)';
    $stmt = $conn->prepare($sql);
    if (!$stmt) { echo json_encode(['success'=>false,'message'=>'Prepare failed: '.$conn->error]); exit; }
    $stmt->bind_param('issssssii', $user_id, $address, $house_no, $city, $postal_code, $blood_group, $last_donation, $donation_count, $emergency_contact);
    if (!$stmt->execute()) { echo json_encode(['success'=>false,'message'=>'Execute failed: '.$stmt->error]); exit; }
    $stmt->close();
}
// If a photo path was provided, store it separately so we don't complicate previous bindings
if ($photo) {
    $up = $conn->prepare('UPDATE user_profiles SET photo = ? WHERE user_id = ?');
    if ($up) {
        $up->bind_param('si', $photo, $user_id);
        $up->execute();
        $up->close();
    }
}

echo json_encode(['success' => true, 'message' => 'Profile saved']);
exit;

?>
