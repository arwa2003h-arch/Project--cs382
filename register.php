<?php
header('Content-Type: application/json');
require_once 'config.php';

// Read register data sent from AJAX
$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$role = $_POST['role'] ?? 'student';

if ($name === '' || $email === '' || $password === '') {
    echo json_encode(['status' => 'error', 'message' => 'Please fill all fields']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid email format']);
    exit;
}

if (!in_array($role, ['student', 'teacher'])) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid role']);
    exit;
}

$db = new Database();

// Check if the email already exists
$check = $db->conn->prepare("SELECT id FROM users WHERE email = ?");
$check->bind_param("s", $email);
$check->execute();
$checkResult = $check->get_result();

if ($checkResult->num_rows > 0) {
    echo json_encode(['status' => 'error', 'message' => 'Email already exists']);
    exit;
}

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
$stmt = $db->conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $name, $email, $hashedPassword, $role);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Account created successfully']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Registration failed']);
}
?>
