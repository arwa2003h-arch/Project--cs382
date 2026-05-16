<?php
// Load assignments for the logged-in student with personal submission status
$sessionPath = __DIR__ . '/sessions';
if (!is_dir($sessionPath)) {
    mkdir($sessionPath, 0777, true);
}
session_save_path($sessionPath);
session_start();

header('Content-Type: application/json');
require_once 'config.php';

$studentId = (int)($_SESSION['user_id'] ?? 0);

if ($studentId <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Please login as a student first.']);
    exit;
}

$db = new Database();
$sql = "
    SELECT
        tasks.id,
        tasks.title,
        tasks.description,
        tasks.deadline,
        COALESCE(courses.name, 'No Course') AS course_name,
        submissions.id AS submission_id
    FROM tasks
    LEFT JOIN courses ON tasks.course_id = courses.id
    LEFT JOIN submissions
        ON tasks.id = submissions.task_id
        AND submissions.student_id = ?
    ORDER BY tasks.deadline ASC
";

$stmt = $db->conn->prepare($sql);
$stmt->bind_param("i", $studentId);
$stmt->execute();
$result = $stmt->get_result();

$tasks = [];
while ($row = $result->fetch_assoc()) {
    if (!empty($row['submission_id'])) {
        $row['status'] = 'Completed';
    } elseif (strtotime($row['deadline']) < strtotime(date('Y-m-d'))) {
        $row['status'] = 'Late';
    } else {
        $row['status'] = 'Pending';
    }

    unset($row['submission_id']);
    $tasks[] = $row;
}

echo json_encode(['status' => 'success', 'tasks' => $tasks]);
?>
