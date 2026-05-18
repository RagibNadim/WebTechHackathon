<?php

header("Content-Type: application/json");

include "../Model/DatabaseConnection.php";

$project_id = $_POST["id"] ?? 0;

$response = [];

if($project_id == 0){

    $response["ok"] = false;

    $response["message"] = "Invalid Project ID";

    echo json_encode($response);

    exit();
}

$db = new DatabaseConnection();

$connection = $db->openConnection();

$result = $db->archiveProject($connection, $project_id);

if($result){

    $response["ok"] = true;

    $response["message"] = "Project archived successfully";

}else{

    $response["ok"] = false;

    $response["message"] = "Project archive failed";
}

echo json_encode($response);

?>