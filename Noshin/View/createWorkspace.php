<?php

session_start();

if(!isset($_SESSION["user_id"])){
    Header("Location: login.php");
    exit();
}

$workspaceNameErr = $_SESSION["workspaceNameErr"] ?? "";
$workspaceDescriptionErr = $_SESSION["workspaceDescriptionErr"] ?? "";

$workspaceName = $_SESSION["workspaceName"] ?? "";
$workspaceDescription = $_SESSION["workspaceDescription"] ?? "";

unset($_SESSION["workspaceNameErr"]);
unset($_SESSION["workspaceDescriptionErr"]);
unset($_SESSION["workspaceName"]);
unset($_SESSION["workspaceDescription"]);

?>

<html>
<head>
    <title>Create Workspace</title>
    <link rel="stylesheet" href="../CSS/style.css">
    <script src="../Controller/JS/validation.js"></script>
</head>

<body>

<h2>Create Workspace</h2>

<form method="post" action="../Controller/createWorkspaceHandler.php" onsubmit="return validateWorkspace()">

<table>

<tr>
    <td>Workspace Name</td>
    <td>
        <input type="text" name="name" id="name" placeholder="Enter workspace name" value="<?php echo $workspaceName; ?>">
    </td>
    <td>
        <p id="workspaceNameErr" style="color:red"><?php echo $workspaceNameErr; ?></p>
    </td>
</tr>

<tr>
    <td>Description</td>
    <td>
        <textarea name="description" id="description" placeholder="Enter description"><?php echo $workspaceDescription; ?></textarea>
    </td>
    <td>
        <p id="workspaceDescriptionErr" style="color:red"><?php echo $workspaceDescriptionErr; ?></p>
    </td>
</tr>

<tr>
    <td></td>
    <td>
        <input type="submit" value="Create Workspace">
    </td>
</tr>

</table>

</form>

<br>

<a href="dashboard.php">Back to Dashboard</a>

</body>
</html>