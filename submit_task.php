<?php
$sessionPath = __DIR__ . '/sessions';

if (!is_dir($sessionPath)) {
    mkdir($sessionPath, 0777, true);
}

session_save_path($sessionPath);
session_start();

require_once 'config.php';

$taskId = (int)($_GET['task_id'] ?? 0);
$studentId = (int)($_SESSION['user_id'] ?? 0);

$task = null;
$status = 'Pending';
$pageMessage = '';

if ($taskId > 0) {
    $db = new Database();

    $stmt = $db->conn->prepare("
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
        WHERE tasks.id = ?
    ");

    $stmt->bind_param("ii", $studentId, $taskId);
    $stmt->execute();

    $result = $stmt->get_result();
    $task = $result->fetch_assoc();

    if ($task) {
        if (!empty($task['submission_id'])) {
            $status = 'Completed';
        } elseif (strtotime($task['deadline']) < strtotime(date('Y-m-d'))) {
            $status = 'Late';
        }
    } else {
        $pageMessage = 'Task not found.';
    }
} else {
    $pageMessage = 'No task selected.';
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Submit Task</title>
    <link rel="stylesheet" href="CSS/style.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="submit_task.js"></script>
</head>
<body>

<div class="dashboard-container">

    <aside class="sidebar">
        <div class="logo-box">
            <div class="logo-icon">W</div>
            <h2>Whiteboard</h2>
        </div>

        <ul class="menu">
            <li><a href="student_dashboard.html">Student Home</a></li>
            <li><a href="submit_task.php?task_id=<?php echo $taskId; ?>" class="active">Submit Task</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </aside>

    <main class="main-content form-page">
        <div class="container">

            <h2>Submit Task</h2>

            <?php if ($task): ?>

                <div class="task-box">
                    <p><strong>Course:</strong> <?php echo htmlspecialchars($task['course_name']); ?></p>
                    <p><strong>Task Title:</strong> <?php echo htmlspecialchars($task['title']); ?></p>
                    <p><strong>Due Date:</strong> <?php echo htmlspecialchars($task['deadline']); ?></p>
                    <p><strong>Description:</strong> <?php echo htmlspecialchars($task['description']); ?></p>
                    <p><strong>Status:</strong> <span id="status"><?php echo $status; ?></span></p>
                </div>

                <?php if ($status == 'Completed'): ?>

                    <div class="message success">
                        This task has already been submitted.
                    </div>

                <?php else: ?>

                    <form id="submitForm" enctype="multipart/form-data">
                        <input type="hidden" id="task_id" name="task_id" value="<?php echo $task['id']; ?>">

                        <label>Write Your Answer:</label>
                        <textarea id="answer" name="answer" rows="6"></textarea>

                        <label>Upload File:</label>
                        <input type="file" id="file" name="file">

                        <button type="button" id="removeFile">Remove File</button>

                        <br>

                        <button type="submit">Submit Task</button>
                    </form>

                <?php endif; ?>

            <?php else: ?>

                <div class="message error">
                    <?php echo $pageMessage; ?>
                </div>

            <?php endif; ?>

            <div id="message"></div>

        </div>
    </main>

</div>

</body>
</html>