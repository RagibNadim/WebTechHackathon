<?php

session_start();

if(!isset($_SESSION["user_id"])){
    Header("Location: login.php");
    exit();
}

$joinErr = $_SESSION["joinErr"] ?? "";

unset($_SESSION["joinErr"]);

?>

<html>
<head>
    <title>Join Workspace</title>
    <link rel="stylesheet" href="../CSS/style.css">
    <script src="../Controller/JS/validation.js"></script>
</head>

<body>

<h2>Join Workspace</h2>

<form method="post" action="../Controller/joinWorkspaceHandler.php" onsubmit="return validateJoinWorkspace()">

<table>

<tr>
    <td>Invite Code</td>
    <td>
        <input type="text" name="invite_code" id="invite_code" placeholder="Enter invite code">
    </td>
    <td>
        <p id="joinErr" style="color:red"><?php echo $joinErr; ?></p>
    </td>
</tr>

<tr>
    <td></td>
    <td>
        <input type="submit" value="Join Workspace">
    </td>
</tr>

</table>

</form>

<br>

<a href="dashboard.php">Back to Dashboard</a>

</body>
</html>