<?php

header("Content-Type: application/json");

include "../Model/DatabaseConnection.php";

session_start();

$response = [];

$user_id = $_SESSION["user_id"] ?? "";
$workspace_id = $_SESSION["workspace_id"] ?? "";
$member_id = $_POST["member_id"] ?? 0;

if(!$user_id || !$workspace_id){
    $response["ok"] = false;
    $response["message"] = "Unauthorized access";

    echo json_encode($response);
    exit();
}

if($member_id == 0){
    $response["ok"] = false;
    $response["message"] = "Invalid member";

    echo json_encode($response);
    exit();
}

$db = new DatabaseConnection();
$connection = $db->openConnection();

$ownerCheck = $db->isWorkspaceOwner($connection, $workspace_id, $user_id);

if($ownerCheck->num_rows == 0){
    $response["ok"] = false;
    $response["message"] = "Only owner can remove members";

    echo json_encode($response);
    exit();
}

$memberResult = $db->getWorkspaceMemberById($connection, $member_id);

if($memberResult->num_rows == 0){
    $response["ok"] = false;
    $response["message"] = "Member not found";

    echo json_encode($response);
    exit();
}

$member = $memberResult->fetch_assoc();

if($member["user_id"] == $user_id){
    $response["ok"] = false;
    $response["message"] = "Owner cannot remove themselves";

    echo json_encode($response);
    exit();
}

$result = $db->removeWorkspaceMember($connection, $member_id);

if($result){
    $response["ok"] = true;
    $response["message"] = "Member removed successfully";
}else{
    $response["ok"] = false;
    $response["message"] = "Member removal failed";
}

echo json_encode($response);

?>