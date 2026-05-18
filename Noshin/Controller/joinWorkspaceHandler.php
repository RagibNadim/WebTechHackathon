<?php

include "../Model/DatabaseConnection.php";

session_start();

$user_id = $_SESSION["user_id"] ?? "";

if(!$user_id){
    Header("Location: ../View/login.php");
    exit();
}

$invite_code = strtoupper(trim($_POST["invite_code"] ?? ""));

if(!$invite_code){
    $_SESSION["joinErr"] = "Invite code is required";
    Header("Location: ../View/joinWorkspace.php");
    exit();
}

$db = new DatabaseConnection();
$connection = $db->openConnection();

$workspaceResult = $db->getWorkspaceByInviteCode($connection, $invite_code);

if($workspaceResult->num_rows == 0){
    $_SESSION["joinErr"] = "Invalid invite code";
    Header("Location: ../View/joinWorkspace.php");
    exit();
}

$workspace = $workspaceResult->fetch_assoc();

$workspace_id = $workspace["id"];

$membership = $db->checkWorkspaceMembership($connection, $workspace_id, $user_id);

if($membership->num_rows > 0){
    $_SESSION["workspace_id"] = $workspace_id;
    $_SESSION["workspaceSuccess"] = "You are already a member of this workspace";

    Header("Location: ../View/dashboard.php");
    exit();
}

$result = $db->addWorkspaceMember($connection, $workspace_id, $user_id);

if($result){
    $_SESSION["workspace_id"] = $workspace_id;
    $_SESSION["workspaceSuccess"] = "Workspace joined successfully";

    Header("Location: ../View/dashboard.php");
    exit();
}else{
    $_SESSION["joinErr"] = "Could not join workspace";
    Header("Location: ../View/joinWorkspace.php");
    exit();
}

?>