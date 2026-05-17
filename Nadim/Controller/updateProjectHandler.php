<?php

include "../Model/DatabaseConnection.php";

$project_id = $_POST["project_id"];
$name = $_POST["name"];
$description = $_POST["description"];
$deadline = $_POST["deadline"];
$color_label = $_POST["color_label"];
$members = $_POST["members"] ?? [];

$db = new DatabaseConnection();
$connection = $db->openConnection();

$db->updateProject($connection, $project_id, $name, $description, $deadline, $color_label);

$db->removeProjectMembers($connection, $project_id);

foreach($members as $member){
    $db->addProjectMember($connection, $project_id, $member);
}

header("Location: ../View/projectList.php");

?>