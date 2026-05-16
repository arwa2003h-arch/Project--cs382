<?php
// Save the selected student's submission in the submissions table
$sessionPath = __DIR__ . '/sessions';
if (!is_dir($sessionPath)) {
    mkdir($sessionPath, 0777, true);
}
session_save_path($sessionPath);
session_start();

header('Content-Type: application/json');
require_once 'config.php';

$studentId = (int)($_SESSION['user_id'] ?? 0);
$taskId = (int)($_POST['task_id'] ?? 0);
$answer = trim($_POST['answer'] ?? '');
$fileName = null;

if ($studentId <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Please login as a student first.']);
    exit;
}

if ($taskId <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid task.']);
    exit;
}

if ($answer === '' && empty($_FILES['file']['name'])) {
    echo json_encode(['status' => 'error', 'message' => 'Please write an answer or upload a file.']);
    exit;
}

if (!empty($_FILES['file']['name'])) {
    $uploadDir = __DIR__ . '/uploads';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $fileName = time() . '_' . basename($_FILES['file']['name']);
    move_uploaded_file($_FILES['file']['tmp_name'], $uploadDir . '/' . $fileName);
}

$db = new Database();

// Insert a new submission or update the old one for the same student and task
$stmt = $db->conn->prepare("
    INSERT INTO submissions (task_id, student_id, answer, file_name)
    VALUES (?, ?, ?, ?)
    ON DUPLICATE KEY UPDATE
        answer = VALUES(answer),
        file_name = VALUES(file_name),
        submitted_at = CURRENT_TIMESTAMP
");
$stmt->bind_param("iiss", $taskId, $studentId, $answer, $fileName);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Task submitted successfully.']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Submission failed.']);
}
?>
