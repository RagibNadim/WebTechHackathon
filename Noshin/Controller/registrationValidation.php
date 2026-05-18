<?php

include "../Model/DatabaseConnection.php";

session_start();

$name = trim($_POST["name"] ?? "");
$email = trim($_POST["email"] ?? "");
$password = $_POST["password"] ?? "";

$hasNameError = true;
$hasEmailError = true;
$hasPasswordError = true;

if(!$name){
    $_SESSION["nameErr"] = "Name is required";
    $hasNameError = true;
}else{
    unset($_SESSION["nameErr"]);
    $hasNameError = false;
}

if(!$email){
    $_SESSION["emailErr"] = "Email is required";
    $hasEmailError = true;
}
elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)){
    $_SESSION["emailErr"] = "Invalid email format";
    $hasEmailError = true;
}else{
    unset($_SESSION["emailErr"]);
    $hasEmailError = false;
}

if(!$password){
    $_SESSION["passwordErr"] = "Password is required";
    $hasPasswordError = true;
}
elseif(strlen($password) < 8){
    $_SESSION["passwordErr"] = "Password must be at least 8 characters";
    $hasPasswordError = true;
}else{
    unset($_SESSION["passwordErr"]);
    $hasPasswordError = false;
}

if($hasNameError || $hasEmailError || $hasPasswordError){

    $_SESSION["name"] = $name;
    $_SESSION["email"] = $email;

    Header("Location: ../View/registration.php");
    exit();
}

$db = new DatabaseConnection();
$connection = $db->openConnection();

$existingUser = $db->getUserByEmail($connection, $email);

if($existingUser->num_rows > 0){
    $_SESSION["emailErr"] = "Email already exists";
    $_SESSION["name"] = $name;
    $_SESSION["email"] = $email;

    Header("Location: ../View/registration.php");
    exit();
}

$password_hash = password_hash($password, PASSWORD_DEFAULT);

$result = $db->createUser($connection, $name, $email, $password_hash);

if($result){
    $_SESSION["success"] = "Registration successful. Please login.";
    Header("Location: ../View/login.php");
    exit();
}else{
    $_SESSION["emailErr"] = "Registration failed";
    Header("Location: ../View/registration.php");
    exit();
}

?>