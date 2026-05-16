<?php
header('Content-Type: application/json');
require_once 'config.php';

$db = new Database();
$action = $_REQUEST['action'] ?? 'dashboard';

if ($action === 'dashboard') {
    // Load teacher tasks from database with course name and submission count
    $sql = "
        SELECT
            tasks.id,
            tasks.title,
            tasks.description,
            tasks.deadline,
            COALESCE(courses.name, 'No Course') AS course_name,
            COUNT(submissions.id) AS submitted_students
        FROM tasks
        LEFT JOIN courses ON tasks.course_id = courses.id
        LEFT JOIN submissions ON tasks.id = submissions.task_id
        GROUP BY tasks.id, tasks.title, tasks.description, tasks.deadline, courses.name
        ORDER BY tasks.deadline ASC
    ";

    $result = $db->conn->query($sql);
    $tasks = [];

    while ($row = $result->fetch_assoc()) {
        $row['status'] = task_status($row['deadline'], (int)$row['submitted_students']);
        $tasks[] = $row;
    }

    send_success('Dashboard data loaded.', $tasks);
}

if ($action === 'delete') {
    $taskId = (int)($_POST['task_id'] ?? 0);

    if ($taskId <= 0) {
        send_error('Invalid task id.');
    }

    // Delete task from database using AJAX request
    $stmt = $db->conn->prepare("DELETE FROM tasks WHERE id = ?");
    $stmt->bind_param("i", $taskId);

    if (!$stmt->execute()) {
        send_error('Delete failed.');
    }

    if ($stmt->affected_rows === 0) {
        send_error('Task not found.');
    }

    $_REQUEST['action'] = 'dashboard';
    $action = 'dashboard';

    $sql = "
        SELECT
            tasks.id,
            tasks.title,
            tasks.description,
            tasks.deadline,
            COALESCE(courses.name, 'No Course') AS course_name,
            COUNT(submissions.id) AS submitted_students
        FROM tasks
        LEFT JOIN courses ON tasks.course_id = courses.id
        LEFT JOIN submissions ON tasks.id = submissions.task_id
        GROUP BY tasks.id, tasks.title, tasks.description, tasks.deadline, courses.name
        ORDER BY tasks.deadline ASC
    ";

    $result = $db->conn->query($sql);
    $tasks = [];

    while ($row = $result->fetch_assoc()) {
        $row['status'] = task_status($row['deadline'], (int)$row['submitted_students']);
        $tasks[] = $row;
    }

    send_success('Task deleted successfully.', $tasks);
}

send_error('Unknown action.');

function send_success($message, $tasks) {
    echo json_encode([
        'status' => 'success',
        'message' => $message,
        'tasks' => array_values($tasks),
        'stats' => calculate_stats($tasks)
    ]);
    exit;
}

function send_error($message) {
    echo json_encode([
        'status' => 'error',
        'message' => $message
    ]);
    exit;
}

function calculate_stats($tasks) {
    $pendingTasks = 0;
    $submittedStudents = 0;

    foreach ($tasks as $task) {
        if ($task['status'] === 'Pending') {
            $pendingTasks++;
        }

        $submittedStudents += (int)$task['submitted_students'];
    }

    return [
        'total_tasks' => count($tasks),
        'submitted_students' => $submittedStudents,
        'pending_tasks' => $pendingTasks
    ];
}

function task_status($deadline, $submittedStudents) {
    if ($submittedStudents > 0) {
        return 'Completed';
    }

    if (strtotime($deadline) < strtotime(date('Y-m-d'))) {
        return 'Late';
    }

    return 'Pending';
}
?>
