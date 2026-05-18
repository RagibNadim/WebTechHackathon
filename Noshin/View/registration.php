<?php

session_start();

if(isset($_SESSION["user_id"])){
    Header("Location: dashboard.php");
    exit();
}

$nameErr = $_SESSION["nameErr"] ?? "";
$emailErr = $_SESSION["emailErr"] ?? "";
$passwordErr = $_SESSION["passwordErr"] ?? "";

$name = $_SESSION["name"] ?? "";
$email = $_SESSION["email"] ?? "";

unset($_SESSION["nameErr"]);
unset($_SESSION["emailErr"]);
unset($_SESSION["passwordErr"]);
unset($_SESSION["name"]);
unset($_SESSION["email"]);

?>

<html>
<head>
    <title>Registration</title>
    <link rel="stylesheet" href="../CSS/style.css">
    <script src="../Controller/JS/validation.js"></script>
</head>

<body>

<h2>User Registration</h2>

<form method="post" action="../Controller/registrationValidation.php" onsubmit="return validateRegistration()">

<table>

<tr>
    <td>Name</td>
    <td>
        <input type="text" name="name" id="name" placeholder="Enter name" value="<?php echo $name; ?>">
    </td>
    <td>
        <p id="nameErr" style="color:red"><?php echo $nameErr; ?></p>
    </td>
</tr>

<tr>
    <td>Email</td>
    <td>
        <input type="text" name="email" id="email" placeholder="Enter email" value="<?php echo $email; ?>">
    </td>
    <td>
        <p id="emailErr" style="color:red"><?php echo $emailErr; ?></p>
    </td>
</tr>

<tr>
    <td>Password</td>
    <td>
        <input type="password" name="password" id="password" placeholder="Enter password">
    </td>
    <td>
        <p id="passwordErr" style="color:red"><?php echo $passwordErr; ?></p>
    </td>
</tr>

<tr>
    <td></td>
    <td>
        <input type="submit" name="submit" value="Register">
    </td>
</tr>

<tr>
    <td></td>
    <td>
        Already have an account? <a href="login.php">Login Here</a>
    </td>
</tr>

</table>

</form>

</body>
</html>