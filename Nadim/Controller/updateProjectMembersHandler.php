<?php

include "../Model/DatabaseConnection.php";

$project_id = $_POST["project_id"];
$members = $_POST["members"] ?? [];

$db = new DatabaseConnection();

$connection = $db->openConnection();

$db->removeProjectMembers($connection, $project_id);

foreach($members as $member){

    $db->addProjectMember($connection, $project_id, $member);
}

header("Location: ../View/projectSettings.php?id=$project_id");

?>