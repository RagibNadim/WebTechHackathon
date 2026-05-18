<?php

session_start();

include "../Model/DatabaseConnection.php";

$user_id = $_SESSION["user_id"] ?? "";
$name = $_SESSION["name"] ?? "";
$workspace_id = $_SESSION["workspace_id"] ?? "";

if(!$user_id){
    Header("Location: login.php");
    exit();
}

$theme = $_COOKIE["theme"] ?? "Default";
$workspaceSuccess = $_SESSION["workspaceSuccess"] ?? "";

unset($_SESSION["workspaceSuccess"]);

$db = new DatabaseConnection();
$connection = $db->openConnection();

$workspaces = $db->getUserWorkspaces($connection, $user_id);

$currentWorkspace = null;

if($workspace_id){
    $workspaceResult = $db->getWorkspaceById($connection, $workspace_id);

    if($workspaceResult->num_rows > 0){
        $currentWorkspace = $workspaceResult->fetch_assoc();
    }
}

?>

<html>
<head>
    <title>Dashboard</title>
    <link rel="stylesheet" href="../CSS/style.css">
</head>

<body>

<h2>Dashboard</h2>

<p>Hello, <?php echo $name; ?></p>

<p style="color:green"><?php echo $workspaceSuccess; ?></p>

<p>Current Theme: <?php echo $theme; ?></p>

<form method="post" action="../Controller/setCookieHandler.php">
    <select name="theme">
        <option value="Default">Default</option>
        <option value="Light">Light</option>
        <option value="Dark">Dark</option>
    </select>

    <input type="submit" value="Set Theme">
</form>

<br>

<?php

if($currentWorkspace){

    echo "<h3>Current Workspace: ".$currentWorkspace["name"]."</h3>";
    echo "<p>Description: ".$currentWorkspace["description"]."</p>";
    echo "<p>Invite Code: <strong>".$currentWorkspace["invite_code"]."</strong></p>";

}else{

    echo "<h3>No Workspace Found</h3>";
    echo "<p>Please create or join a workspace.</p>";
}

?>

<br>

<form method="get" action="../Controller/switchWorkspace.php">

<select name="id">

<?php

if($workspaces->num_rows > 0){

    while($row = $workspaces->fetch_assoc()){

        $selected = "";

        if($row["id"] == $workspace_id){
            $selected = "selected";
        }

        echo "<option value='".$row["id"]."' $selected>".$row["name"]."</option>";
    }
}

?>

</select>

<input type="submit" value="Switch Workspace">

</form>

<br>

<a href="createWorkspace.php">Create Workspace</a>
|
<a href="joinWorkspace.php">Join Workspace</a>
|
<a href="workspaceSettings.php">Workspace Settings</a>
|
<a href="../../Nadim/View/projectList.php">Go To Projects</a>
|
<a href="../../tonmoy/View/taskBoard.php">Go To Task Board</a>
|
<a href="../../abid/View/activityFeed.php">Go To Activity Feed</a>
|
<a href="../Controller/logout.php">Logout</a>

</body>
</html>