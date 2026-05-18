<?php

session_start();

include "../Model/DatabaseConnection.php";

$workspace_id = $_SESSION["workspace_id"] ?? "";

if(!$workspace_id){
    Header("Location: ../../Noshin/View/login.php");
    exit();
}

$project_id = $_POST["project_id"] ?? 0;
$name = trim($_POST["name"] ?? "");
$description = trim($_POST["description"] ?? "");
$deadline = $_POST["deadline"] ?? "";
$color_label = $_POST["color_label"] ?? "";
$members = $_POST["members"] ?? [];

$allowedColors = ["#e74c3c", "#3498db", "#2ecc71", "#f39c12", "#9b59b6"];

if($project_id == 0){
    die("Invalid Project ID");
}

if($name == "" || $description == "" || $deadline == "" || !in_array($color_label, $allowedColors) || count($members) < 1){
    die("Invalid project update data");
}

$db = new DatabaseConnection();
$connection = $db->openConnection();

$projectCheck = $db->getProjectById($connection, $project_id, $workspace_id);

if($projectCheck->num_rows == 0){
    die("Project access denied");
}

$db->updateProject($connection, $project_id, $name, $description, $deadline, $color_label);

$db->removeProjectMembers($connection, $project_id);

foreach($members as $member){
    $db->addProjectMember($connection, $project_id, $member);
}

Header("Location: ../View/projectList.php");
exit();

?>