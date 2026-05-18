<?php

include "../Model/DatabaseConnection.php";

session_start();

$email = trim($_POST["email"] ?? "");
$password = $_POST["password"] ?? "";

$hasEmailError = true;
$hasPasswordError = true;

if(!$email){
    $_SESSION["emailErr"] = "Email is required";
    $hasEmailError = true;
}else{
    unset($_SESSION["emailErr"]);
    $hasEmailError = false;
}

if(!$password){
    $_SESSION["passwordErr"] = "Password is required";
    $hasPasswordError = true;
}else{
    unset($_SESSION["passwordErr"]);
    $hasPasswordError = false;
}

if($hasEmailError || $hasPasswordError){

    $_SESSION["email"] = $email;

    Header("Location: ../View/login.php");
    exit();
}

$db = new DatabaseConnection();
$connection = $db->openConnection();

$userResult = $db->getUserByEmail($connection, $email);

if($userResult->num_rows == 1){

    $user = $userResult->fetch_assoc();

    if(password_verify($password, $user["password_hash"])){

        $_SESSION["user_id"] = $user["id"];
        $_SESSION["name"] = $user["name"];

        $workspaceResult = $db->getFirstWorkspaceByUserId($connection, $user["id"]);

        if($workspaceResult->num_rows > 0){
            $workspace = $workspaceResult->fetch_assoc();
            $_SESSION["workspace_id"] = $workspace["id"];
        }else{
            $_SESSION["workspace_id"] = null;
        }

        Header("Location: ../View/dashboard.php");
        exit();

    }else{
        $_SESSION["loginErr"] = "Email or password does not match";
        Header("Location: ../View/login.php");
        exit();
    }

}else{
    $_SESSION["loginErr"] = "Email or password does not match";
    Header("Location: ../View/login.php");
    exit();
}

?>