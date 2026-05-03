<?php
if(isset($_POST["task_id"]) && isset($_POST["answer"])){
    echo "Task submitted successfully.";
}
else{
    echo "Submission failed.";
}
?>