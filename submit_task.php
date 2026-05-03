<?php
$taskTitle = "Database Assignment";
$dueDate = "2026-05-10";
$taskDescription = "Submit your database assignment before the deadline.";
$status = "Pending";
?>

<!DOCTYPE html>
<html>
<head>
    <title>Submit Task</title>
    <link rel="stylesheet" href="submit_task.css">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="submit_task.js"></script>
</head>
<body>

    <div class="container">

        <div class="top-bar">
            <a href="index.html" class="back-btn">Back to Home</a>
        </div>

        <h2>Submit Task</h2>

        <div class="task-box">
            <p><strong>Task Title:</strong> <?php echo $taskTitle; ?></p>
            <p><strong>Due Date:</strong> <?php echo $dueDate; ?></p>
            <p><strong>Description:</strong> <?php echo $taskDescription; ?></p>
            <p><strong>Status:</strong> <span id="status"><?php echo $status; ?></span></p>
        </div>

        <form id="submitForm">
            <input type="hidden" id="task_id" name="task_id" value="1">

            <label>Write Your Answer:</label>
            <textarea id="answer" name="answer" rows="6"></textarea>

            <label>Upload File:</label>
            <input type="file" id="file" name="file">
            <button type="button" id="removeFile">Remove File</button>

            <br>

            <button type="submit">Submit Task</button>
        </form>

        <div id="message"></div>

    </div>

</body>
</html>