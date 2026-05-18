<?php

include "../Model/DatabaseConnection.php";

session_start();

$user_id = $_SESSION["user_id"] ?? "";
$workspace_id = $_GET["id"] ?? 0;

if(!$user_id){
    Header("Location: ../View/login.php");
    exit();
}

if($workspace_id == 0){
    Header("Location: ../View/dashboard.php");
    exit();
}

$db = new DatabaseConnection();
$connection = $db->openConnection();

$membership = $db->checkWorkspaceMembership($connection, $workspace_id, $user_id);

if($membership->num_rows == 1){

    $_SESSION["workspace_id"] = $workspace_id;

    Header("Location: ../View/dashboard.php");
    exit();

}else{
    $_SESSION["workspaceSuccess"] = "Invalid workspace access";
    Header("Location: ../View/dashboard.php");
    exit();
}

?>