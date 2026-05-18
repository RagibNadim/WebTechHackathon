<?php

include "../Model/DatabaseConnection.php";

session_start();

$user_id = $_SESSION["user_id"] ?? "";

if(!$user_id){
    Header("Location: ../View/login.php");
    exit();
}

$name = trim($_POST["name"] ?? "");
$description = trim($_POST["description"] ?? "");

$hasNameError = true;
$hasDescriptionError = true;

if(!$name){
    $_SESSION["workspaceNameErr"] = "Workspace name is required";
    $hasNameError = true;
}else{
    unset($_SESSION["workspaceNameErr"]);
    $hasNameError = false;
}

if(!$description){
    $_SESSION["workspaceDescriptionErr"] = "Workspace description is required";
    $hasDescriptionError = true;
}else{
    unset($_SESSION["workspaceDescriptionErr"]);
    $hasDescriptionError = false;
}

if($hasNameError || $hasDescriptionError){

    $_SESSION["workspaceName"] = $name;
    $_SESSION["workspaceDescription"] = $description;

    Header("Location: ../View/createWorkspace.php");
    exit();
}

$db = new DatabaseConnection();
$connection = $db->openConnection();

$invite_code = strtoupper(substr(str_shuffle("ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"), 0, 6));

$workspace_id = $db->createWorkspace($connection, $name, $description, $user_id, $invite_code);

if($workspace_id){

    $db->addWorkspaceMember($connection, $workspace_id, $user_id);

    $_SESSION["workspace_id"] = $workspace_id;
    $_SESSION["workspaceSuccess"] = "Workspace created successfully";

    Header("Location: ../View/dashboard.php");
    exit();

}else{
    $_SESSION["workspaceNameErr"] = "Workspace creation failed";
    Header("Location: ../View/createWorkspace.php");
    exit();
}

?>