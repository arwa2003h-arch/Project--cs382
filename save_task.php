<?php
header('Content-Type: application/json');

$task_id = $_POST['task_id'] ?? '';
$title = $_POST['title'] ?? '';
$deadline = $_POST['deadline'] ?? '';

if (empty($title) || empty($deadline)) {
    echo json_encode(['status' => 'error', 'message' => 'Missing title or deadline']);
    exit;
}

if ($task_id == '') {
    echo json_encode(['status' => 'success', 'message' => "Task '$title' added"]);
} else {
    echo json_encode(['status' => 'success', 'message' => "Task ID $task_id updated"]);
}
?>