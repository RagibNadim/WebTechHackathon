<?php

session_start();

include "../Model/DatabaseConnection.php";

$project_id = $_GET["id"] ?? 0;
$workspace_id = $_SESSION["workspace_id"] ?? 1;

if($project_id == 0){
    die("Invalid Project ID");
}

$db = new DatabaseConnection();
$connection = $db->openConnection();

$project = $db->getProjectById($connection, $project_id);
$data = $project->fetch_assoc();

if(!$data){
    die("Project Not Found");
}

$members = $db->getWorkspaceMembers($connection, $workspace_id);

$projectMemberIds = [];

$projectMembers = $db->getProjectMemberIds($connection, $project_id);

while($memberRow = $projectMembers->fetch_assoc()){
    $projectMemberIds[] = $memberRow["user_id"];
}

?>

<html>
<head>
    <title>Project Settings</title>
    <link rel="stylesheet" href="../CSS/style.css">
</head>

<body>

<h2>Project Settings</h2>

<form method="post" action="../Controller/updateProjectHandler.php">

<input type="hidden" name="project_id" value="<?php echo $project_id; ?>">

<table>

<tr>
    <td>Project Name</td>
    <td>
        <input type="text" name="name" value="<?php echo $data['name']; ?>">
    </td>
</tr>

<tr>
    <td>Description</td>
    <td>
        <textarea name="description"><?php echo $data['description']; ?></textarea>
    </td>
</tr>

<tr>
    <td>Deadline</td>
    <td>
        <input type="date" name="deadline" value="<?php echo $data['deadline']; ?>">
    </td>
</tr>

<tr>
    <td>Color Label</td>
    <td>
        <select name="color_label">
            <option value="red" <?php if($data['color_label'] == "red"){ echo "selected"; } ?>>Red</option>
            <option value="blue" <?php if($data['color_label'] == "blue"){ echo "selected"; } ?>>Blue</option>
            <option value="green" <?php if($data['color_label'] == "green"){ echo "selected"; } ?>>Green</option>
        </select>
    </td>
</tr>

<tr>
    <td>Assign Members</td>
    <td>
        <?php

        while($row = $members->fetch_assoc()){

            $member_id = $row["id"];
            $member_name = $row["name"];

            $checked = "";

            if(in_array($member_id, $projectMemberIds)){
                $checked = "checked";
            }

            echo "<input type='checkbox' name='members[]' value='$member_id' $checked> $member_name <br>";
        }

        ?>
    </td>
</tr>

<tr>
    <td></td>
    <td>
        <input type="submit" value="Update Project">
    </td>
</tr>

</table>

</form>

<br>

<a href="projectList.php">Back to Project List</a>

</body>
</html>