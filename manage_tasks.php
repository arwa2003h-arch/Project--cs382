<?php
header('Content-Type: application/json');

$action = $_REQUEST['action'] ?? 'dashboard';

$tasks = [
    [
        'id' => 1,
        'title' => 'Assignment 1',
        'description' => 'Solve the first set of course exercises.',
        'deadline' => '2026-05-20',
        'priority' => 'High',
        'status' => 'Pending',
        'course_id' => 'CS382',
        'submitted_students' => 8
    ],
    [
        'id' => 2,
        'title' => 'Database Project',
        'description' => 'Submit the database design and SQL script.',
        'deadline' => '2026-05-28',
        'priority' => 'Medium',
        'status' => 'Completed',
        'course_id' => 'CS382',
        'submitted_students' => 14
    ],
    [
        'id' => 3,
        'title' => 'Interface Report',
        'description' => 'Upload the Phase 2 interface screenshots.',
        'deadline' => '2026-05-14',
        'priority' => 'Low',
        'status' => 'Pending',
        'course_id' => 'CS382',
        'submitted_students' => 5
    ]
];

if ($action === 'dashboard') {
    send_success('Dashboard data loaded.', $tasks);
}

if ($action === 'delete') {
    $taskId = (int)($_POST['task_id'] ?? 0);

    if ($taskId <= 0) {
        send_error('Invalid task id.');
    }

    $originalCount = count($tasks);
    $tasks = array_values(array_filter(
        $tasks,
        function ($task) use ($taskId) {
            return (int)$task['id'] !== $taskId;
        }
    ));

    if (count($tasks) === $originalCount) {
        send_error('Task not found.');
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
        if (strtolower($task['status']) === 'pending') {
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
?>
