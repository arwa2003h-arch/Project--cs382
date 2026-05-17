$(document).ready(function(){

    $("#removeFile").hide();

    $("#file").change(function(){
        if($("#file").val() != ""){
            $("#removeFile").show();
        } else {
            $("#removeFile").hide();
        }
    });

    $("#removeFile").click(function(){
        $("#file").val("");
        $("#removeFile").hide();
    });

    $("#submitForm").submit(function(e){
        e.preventDefault();

        var answer = $("#answer").val();
        var file = $("#file").val();

        if(answer == "" && file == ""){
            $("#message").html("Please write your answer or upload a file.");
            $("#message").css("color", "red");
            return;
        }

        var formData = new FormData(this);

        $.ajax({
            url: "submit_task_action.php",
            type: "POST",
            data: formData,
            dataType: "json",
            contentType: false,
            processData: false,

            success: function(response){
                if(response.status == "success"){
                    $("#message").html(response.message);
                    $("#message").css("color", "green");
                    $("#status").text("Completed");
                    $("#submitForm").hide();
                } else {
                    $("#message").html(response.message);
                    $("#message").css("color", "red");
                }
            },

            error: function(){
                    $("#message").html("Submission error.");
                     $("#message").css("color", "red");
}
        });
    });

});