$(document).ready(function(){

    $("#submitForm").submit(function(e){
        e.preventDefault();

        var answer = $("#answer").val();
        var file = $("#file").val();

        if(answer == "" && file == ""){
            $("#message").html("Please write your answer or upload a file.");
            $("#message").css("color", "red");
        }
        else{
            $.post("submit_task_action.php",
            {
                task_id: $("#task_id").val(),
                answer: answer
            },
            function(data){
                $("#message").html(data);
                $("#message").css("color", "green");
                $("#status").text("Completed");
            });
        }

    });

});