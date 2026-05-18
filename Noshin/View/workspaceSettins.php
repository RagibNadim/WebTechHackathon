<?php

session_start();

include "../Model/DatabaseConnection.php";

$user_id = $_SESSION["user_id"] ?? "";
$workspace_id = $_SESSION["workspace_id"] ?? "";

if(!$user_id){
    Header("Location: login.php");
    exit();
}

if(!$workspace_id){
    Header("Location: dashboard.php");
    exit();
}

$db = new DatabaseConnection();
$connection = $db->openConnection();

$workspaceResult = $db->getWorkspaceById($connection, $workspace_id);

if($workspaceResult->num_rows == 0){
    die("Workspace Not Found");
}

$workspace = $workspaceResult->fetch_assoc();

$ownerCheck = $db->isWorkspaceOwner($connection, $workspace_id, $user_id);

$isOwner = false;

if($ownerCheck->num_rows > 0){
    $isOwner = true;
}

$members = $db->getWorkspaceMembers($connection, $workspace_id);

?>

<html>
<head>
    <title>Workspace Settings</title>
    <link rel="stylesheet" href="../CSS/style.css">
    <script src="../Controller/JS/ajax.js"></script>
</head>

<body>

<h2>Workspace Settings</h2>

<p id="message" style="color:green"></p>

<h3><?php echo $workspace["name"]; ?></h3>

<p><?php echo $workspace["description"]; ?></p>

<p>Invite Code: <strong><?php echo $workspace["invite_code"]; ?></strong></p>

<table border="1">

<tr>
    <th>Member Name</th>
    <th>Email</th>
    <th>Join Date</th>
    <th>Action</th>
</tr>

<?php

if($members->num_rows > 0){

    while($row = $members->fetch_assoc()){

        $member_id = $row["member_id"];
        $member_user_id = $row["user_id"];
        $member_name = $row["name"];
        $member_email = $row["email"];
        $joined_at = $row["joined_at"];

        echo "<tr id='memberRow$member_id'>
                <td>$member_name</td>
                <td>$member_email</td>
                <td>$joined_at</td>
                <td>";

        if($isOwner && $member_user_id != $user_id){
            echo "<button type='button' onclick='removeMember($member_id)'>Remove</button>";
        }else{
            echo "Not Allowed";
        }

        echo "</td>
              </tr>";
    }

}else{
    echo "<tr>
            <td colspan='4'>No Members Found</td>
          </tr>";
}

?>

</table>

<br>

<a href="dashboard.php">Back to Dashboard</a>

</body>
</html>