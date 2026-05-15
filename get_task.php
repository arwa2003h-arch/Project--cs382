<?php
header('Content-Type: application/json');
require_once 'config.php';
// B
$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid task ID']);
    exit;
}

$db = new Database();
$stmt = $db->conn->prepare("SELECT id, title, description, deadline, course_id FROM tasks WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    echo json_encode(['status' => 'success', 'task' => $row]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Task not found']);
}
?>