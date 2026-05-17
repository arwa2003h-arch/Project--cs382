<?php
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
$fileName = '';

if ($studentId <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Please login as a student first.']);
    exit;
}

if ($taskId <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid task.']);
    exit;
}

if ($answer == '' && empty($_FILES['file']['name'])) {
    echo json_encode(['status' => 'error', 'message' => 'Please write an answer or upload a file.']);
    exit;
}

$db = new Database();

$taskStmt = $db->conn->prepare("SELECT deadline FROM tasks WHERE id = ?");
$taskStmt->bind_param("i", $taskId);
$taskStmt->execute();
$taskResult = $taskStmt->get_result();
$taskData = $taskResult->fetch_assoc();

if (!$taskData) {
    echo json_encode(['status' => 'error', 'message' => 'Task not found.']);
    exit;
}

$taskStatus = 'Completed';
$message = 'Task submitted successfully.';

if (strtotime($taskData['deadline']) < strtotime(date('Y-m-d'))) {
    $taskStatus = 'Late';
    $message = 'Task submitted late.';
}

$check = $db->conn->prepare("SELECT id FROM submissions WHERE task_id = ? AND student_id = ?");
$check->bind_param("ii", $taskId, $studentId);
$check->execute();
$checkResult = $check->get_result();

if ($checkResult->num_rows > 0) {
    echo json_encode(['status' => 'error', 'message' => 'This task has already been submitted.']);
    exit;
}

if (!empty($_FILES['file']['name'])) {
    $uploadDir = __DIR__ . '/uploads';

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $fileName = time() . '_' . basename($_FILES['file']['name']);
    $filePath = $uploadDir . '/' . $fileName;

    if (!move_uploaded_file($_FILES['file']['tmp_name'], $filePath)) {
        echo json_encode(['status' => 'error', 'message' => 'File upload failed.']);
        exit;
    }
}

$stmt = $db->conn->prepare("INSERT INTO submissions (task_id, student_id, answer, file_name) VALUES (?, ?, ?, ?)");
$stmt->bind_param("iiss", $taskId, $studentId, $answer, $fileName);

if ($stmt->execute()) {
    echo json_encode([
        'status' => 'success',
        'message' => $message,
        'task_status' => $taskStatus
    ]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Submission failed.']);
}
?>