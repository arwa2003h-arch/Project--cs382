<?php
header('Content-Type: application/json');
require_once 'config.php';
// b
$db = new Database();
$task_id = $_POST['task_id'] ?? '';
$title = trim($_POST['title'] ?? '');
$description = $_POST['description'] ?? '';
$deadline = $_POST['deadline'] ?? '';
$course_id = $_POST['course_id'] ?? null;
$course_id = empty($course_id) ? null : (int)$course_id;

if (empty($title) || empty($deadline)) {
    echo json_encode(['status' => 'error', 'message' => 'Title and deadline are required']);
    exit;
}

if (empty($task_id)) {
    // INSERT new task
    $stmt = $db->conn->prepare("INSERT INTO tasks (title, description, deadline, course_id) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssi", $title, $description, $deadline, $course_id);
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => "Task '$title' added successfully"]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Database insert failed']);
    }
} else {
    // UPDATE existing task
    $stmt = $db->conn->prepare("UPDATE tasks SET title=?, description=?, deadline=?, course_id=? WHERE id=?");
    $stmt->bind_param("sssii", $title, $description, $deadline, $course_id, $task_id);
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => "Task ID $task_id updated"]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Update failed']);
    }
}
?>