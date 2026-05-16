$(document).ready(function(){

    $("#file").change(function(){
        if($("#file").val() != ""){
            $("#removeFile").show();
        }
    });

    $("#removeFile").click(function(){
        $("#file").val("");
        $("#removeFile").hide();
    });

    $("#submitForm").submit(function(e){
        e.preventDefault();

        let answer = $("#answer").val();
        let file = $("#file").val();

        if(answer == "" && file == ""){
            $("#message").html("Please write your answer or upload a file.");
            $("#message").css("color", "red");
            return;
        }

        // Send the selected task submission using FormData for text and file
        let formData = new FormData(this);

        $.ajax({
            url: "submit_task_action.php",
            type: "POST",
            data: formData,
            dataType: "json",
            contentType: false,
            processData: false,
            success: function(response){
                if(response.status == "success"){
                    $("#message").html(response.message).css("color", "green");
                    $("#status").text("Completed");
                } else {
                    $("#message").html(response.message).css("color", "red");
                }
            },
            error: function(){
                $("#message").html("Connection error.").css("color", "red");
            }
        });
    });

});
