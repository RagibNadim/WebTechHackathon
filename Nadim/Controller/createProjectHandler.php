<?php

session_start();

include "../Model/DatabaseConnection.php";

$workspace_id = $_SESSION["workspace_id"] ?? "";

if(!$workspace_id){
    Header("Location: ../../Noshin/View/login.php");
    exit();
}

$name = trim($_POST["name"] ?? "");
$description = trim($_POST["description"] ?? "");
$deadline = $_POST["deadline"] ?? "";
$color_label = $_POST["color_label"] ?? "";
$members = $_POST["members"] ?? [];

$allowedColors = ["#e74c3c", "#3498db", "#2ecc71", "#f39c12", "#9b59b6"];

$hasError = false;

if($name == ""){
    $_SESSION["nameErr"] = "Project name is required";
    $hasError = true;
}else{
    unset($_SESSION["nameErr"]);
}

if($description == ""){
    $_SESSION["descriptionErr"] = "Description is required";
    $hasError = true;
}else{
    unset($_SESSION["descriptionErr"]);
}

if($deadline == ""){
    $_SESSION["deadlineErr"] = "Deadline is required";
    $hasError = true;
}else{
    unset($_SESSION["deadlineErr"]);
}

if(!in_array($color_label, $allowedColors)){
    $_SESSION["colorErr"] = "Select a valid color";
    $hasError = true;
}else{
    unset($_SESSION["colorErr"]);
}

if(count($members) < 1){
    $_SESSION["memberErr"] = "Select at least one member";
    $hasError = true;
}else{
    unset($_SESSION["memberErr"]);
}

$_SESSION["project_name"] = $name;
$_SESSION["project_description"] = $description;
$_SESSION["project_deadline"] = $deadline;
$_SESSION["project_color"] = $color_label;

if($hasError){
    Header("Location: ../View/createProject.php");
    exit();
}

$db = new DatabaseConnection();
$connection = $db->openConnection();

$project_id = $db->createProject($connection, $workspace_id, $name, $description, $deadline, $color_label);

if($project_id){

    foreach($members as $member){
        $db->addProjectMember($connection, $project_id, $member);
    }

    unset($_SESSION["project_name"]);
    unset($_SESSION["project_description"]);
    unset($_SESSION["project_deadline"]);
    unset($_SESSION["project_color"]);

    Header("Location: ../View/projectList.php");
    exit();

}else{
    $_SESSION["nameErr"] = "Project creation failed";
    Header("Location: ../View/createProject.php");
    exit();
}

?>