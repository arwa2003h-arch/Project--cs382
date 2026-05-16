<?php
// Save session files inside the project to avoid Laragon temp permission issues
$sessionPath = __DIR__ . '/sessions';
if (!is_dir($sessionPath)) {
    mkdir($sessionPath, 0777, true);
}
session_save_path($sessionPath);
session_start();
header('Content-Type: application/json');
require_once 'config.php';

// Read login data sent from AJAX
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if ($email === '' || $password === '') {
    echo json_encode(['status' => 'error', 'message' => 'Please fill all fields']);
    exit;
}

$db = new Database();
$stmt = $db->conn->prepare("SELECT id, name, email, password, role FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($user = $result->fetch_assoc()) {
    if (password_verify($password, $user['password'])) {
        // Store user information in session after successful login
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['role'] = $user['role'];

        $redirect = $user['role'] === 'teacher'
            ? 'teacher_dashboard.html'
            : 'student_dashboard.html';

        echo json_encode([
            'status' => 'success',
            'message' => 'Login successful',
            'role' => $user['role'],
            'redirect' => $redirect
        ]);
        exit;
    }
}

echo json_encode(['status' => 'error', 'message' => 'Invalid email or password']);
?>
