<?php

session_start();

if(isset($_SESSION["user_id"])){
    Header("Location: dashboard.php");
    exit();
}

$emailErr = $_SESSION["emailErr"] ?? "";
$passwordErr = $_SESSION["passwordErr"] ?? "";
$loginErr = $_SESSION["loginErr"] ?? "";
$success = $_SESSION["success"] ?? "";

$email = $_SESSION["email"] ?? "";

unset($_SESSION["emailErr"]);
unset($_SESSION["passwordErr"]);
unset($_SESSION["loginErr"]);
unset($_SESSION["success"]);
unset($_SESSION["email"]);

?>

<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" href="../CSS/style.css">
    <script src="../Controller/JS/validation.js"></script>
</head>

<body>

<h2>User Login</h2>

<p style="color:green"><?php echo $success; ?></p>

<form method="post" action="../Controller/loginValidation.php" onsubmit="return validateLogin()">

<table>

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
        <p style="color:red"><?php echo $loginErr; ?></p>
    </td>
</tr>

<tr>
    <td></td>
    <td>
        <input type="submit" name="submit" value="Login">
    </td>
</tr>

<tr>
    <td></td>
    <td>
        Don't have an account? <a href="registration.php">Register Here</a>
    </td>
</tr>

</table>

</form>

</body>
</html>