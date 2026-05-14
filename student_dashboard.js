$(document).ready(function () {

    let tasks = [];

    $.ajax({
        url: "manage_tasks.php",
        type: "GET",
        data: { action: "dashboard" },
        dataType: "json",

        success: function(response) {
            if (response.status === "success") {
                tasks = response.tasks;
                loadTasks("All");
            } else {
                $("#taskList").html("<p class='no-data'>No assignments found.</p>");
            }
        },

        error: function() {
            $("#taskList").html(
                "<p class='no-data'>Error loading tasks.</p>"
            );
        }
    });

    $(".filter-btn").click(function () {
        $(".filter-btn").removeClass("active");
        $(this).addClass("active");

        let selectedFilter = $(this).data("filter");
        loadTasks(selectedFilter);
    });

    setInterval(updateCountdowns, 1000);

    function loadTasks(filter) {
        let taskList = $("#taskList");
        taskList.empty();

        let filteredTasks = filter === "All"
            ? tasks
            : tasks.filter(task => task.status === filter);

        updateSummaryAndProgress();

        if (filteredTasks.length === 0) {
            taskList.html("<p class='no-data'>No assignments found.</p>");
            return;
        }

        filteredTasks.forEach(function (task) {
            let buttonText = task.status === "Completed" ? "View Submission" : "Submit";

            let taskCard = `
                <div class="task-card">
                    <h3>${task.course_id}</h3>
                    <p><strong>Assignment:</strong> ${task.title}</p>
                    <p><strong>Description:</strong> ${task.description}</p>
                    <p><strong>Due Date:</strong> ${formatDate(task.deadline)}</p>
                    <p><strong>Priority:</strong> ${task.priority}</p>
                    <span class="status ${task.status}">${task.status}</span>
                    <div class="countdown" data-date="${task.deadline}">Loading timer...</div>

                    <a class="submit-btn" href="submit_task.php?task_id=${task.id}">
                        ${buttonText}
                    </a>
                </div>
            `;

            taskList.append(taskCard);
        });

        updateCountdowns();
    }

    function updateSummaryAndProgress() {
        let total = tasks.length;
        let completed = tasks.filter(task => task.status === "Completed").length;
        let pending = tasks.filter(task => task.status === "Pending").length;
        let late = tasks.filter(task => task.status === "Late").length;

        $("#completedCount").text(completed);
        $("#pendingCount").text(pending);
        $("#lateCount").text(late);

        let progress = total === 0 ? 0 : Math.round((completed / total) * 100);

        $("#progressPercent").text(progress + "%");
        $("#progressFill").css("width", progress + "%");
        $("#progressText").text(completed + " out of " + total + " assignments completed.");
    }

    function updateCountdowns() {
        $(".countdown").each(function () {
            let dueDate = new Date($(this).data("date")).getTime();
            let now = new Date().getTime();
            let distance = dueDate - now;

            if (distance <= 0) {
                $(this).text("Time is over");
                return;
            }

            let days = Math.floor(distance / (1000 * 60 * 60 * 24));
            let hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            let minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            let seconds = Math.floor((distance % (1000 * 60)) / 1000);

            $(this).text(days + "d " + hours + "h " + minutes + "m " + seconds + "s left");
        });
    }

    function formatDate(dateText) {
        let date = new Date(dateText);

        return date.toLocaleDateString("en-US", {
            year: "numeric",
            month: "short",
            day: "numeric"
        });
    }

});