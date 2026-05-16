$(document).ready(function () {
    let tasks = [];

    // Load tasks and statistics from PHP using AJAX
    $.ajax({
        url: "manage_tasks.php",
        type: "GET",
        data: { action: "dashboard" },
        dataType: "json",
        success: function (response) {
            if (response.status === "success") {
                tasks = response.tasks;
                showTasks(tasks);
                showStats(response.stats);
            } else {
                showMessage(response.message, "error");
            }
        },
        error: function () {
            showMessage("Please open the page using localhost, not as a file.", "error");
        }
    });
// View task details
$("#tasksTable").on("click", ".view-btn", function () {

    let taskId = $(this).data("id");

    let task = findTask(taskId);

    if (task) {

        $("#modalTitle").text(task.title);

        $("#modalDescription").text(task.description);

        $("#modalDeadline").text(task.deadline);

        $("#modalCourse").text(task.course_name);

        $("#modalSubmissions").text(task.submitted_students);

        $("#taskModal").addClass("open");

    }

});
// Go to edit page
$("#tasksTable").on("click", ".edit-btn", function () {

    let taskId = $(this).data("id");

    window.location.href =
        "task_form.html?id=" + taskId;

});
// Delete task using AJAX
$("#tasksTable").on("click", ".delete-btn", function () {

    let taskId = $(this).data("id");

    if (confirm("Are you sure you want to delete this task?")) {

        $.ajax({

            url: "manage_tasks.php",

            type: "POST",

            data: {
                action: "delete",
                task_id: taskId
            },

            dataType: "json",

            success: function (response) {

                if (response.status === "success") {

                    tasks = response.tasks;

                    showTasks(tasks);

                    showStats(response.stats);

                    showMessage(response.message, "success");

                } else {

                    showMessage(response.message, "error");

                }

            }

        });

    }

});
    // Search/filter tasks in the table
    $("#searchInput").on("keyup", function () {
        let searchText = $(this).val().toLowerCase();

        $("#tasksTable tbody tr").filter(function () {
            $(this).toggle($(this).text().toLowerCase().indexOf(searchText) > -1);
        });
    });

    // Close details popup if it is used later
    $(".close-modal").click(function () {
        $("#taskModal").removeClass("open");
    });

   function showTasks(taskList) {

    let rows = "";

    if (taskList.length === 0) {

        rows =
        '<tr><td colspan="4" class="empty-row">No tasks found.</td></tr>';

    }

    $.each(taskList, function (index, task) {

        rows += `
            <tr>

                <td>${task.title}</td>

                <td>${task.course_name}</td>

                <td>${task.deadline}</td>

                <td>

                    <button class="view-btn"
                        data-id="${task.id}">
                        View
                    </button>

                    <button class="edit-btn"
                        data-id="${task.id}">
                        Edit
                    </button>

                    <button class="delete-btn"
                        data-id="${task.id}">
                        Delete
                    </button>

                </td>

            </tr>
        `;

    });

    $("#tasksTable tbody").html(rows);

}

    function showStats(stats) {
        $("#totalTasks").text(stats.total_tasks);
        $("#submittedTasks").text(stats.submitted_students);
        $("#pendingTasks").text(stats.pending_tasks);
    }
function findTask(taskId) {

    return tasks.find(function (task) {

        return task.id == taskId;

    });

}
    function showMessage(text, type) {
        $("#messageBox")
            .removeClass("success error")
            .addClass(type)
            .text(text)
            .show();
    }
});
