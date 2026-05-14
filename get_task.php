<?php
header('Content-Type: application/json');

$id = $_GET['id'] ?? 0;


$tasks = [
    1 => ['id' => 1, 'title' => 'Submit report', 'description' => 'Phase 2', 'deadline' => '2026-05-15', 'priority' => 'High', 'course_id' => 'CS382']
];

if (isset($tasks[$id])) {
    echo json_encode(['status' => 'success', 'task' => $tasks[$id]]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Task not found']);
}
?>