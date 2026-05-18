<?php

session_start();

include "../Model/DatabaseConnection.php";

$workspace_id = $_SESSION["workspace_id"] ?? "";

if(!$workspace_id){
    Header("Location: ../../Noshin/View/login.php");
    exit();
}

$nameErr = $_SESSION["nameErr"] ?? "";
$descriptionErr = $_SESSION["descriptionErr"] ?? "";
$deadlineErr = $_SESSION["deadlineErr"] ?? "";
$colorErr = $_SESSION["colorErr"] ?? "";
$memberErr = $_SESSION["memberErr"] ?? "";

$name = $_SESSION["project_name"] ?? "";
$description = $_SESSION["project_description"] ?? "";
$deadline = $_SESSION["project_deadline"] ?? "";
$color_label = $_SESSION["project_color"] ?? "";

unset($_SESSION["nameErr"]);
unset($_SESSION["descriptionErr"]);
unset($_SESSION["deadlineErr"]);
unset($_SESSION["colorErr"]);
unset($_SESSION["memberErr"]);

$db = new DatabaseConnection();
$connection = $db->openConnection();

$members = $db->getWorkspaceMembers($connection, $workspace_id);

?>

<html>
<head>
    <title>Create Project</title>
    <link rel="stylesheet" href="../CSS/style.css">
    <script src="../Controller/JS/validation.js"></script>
</head>

<body>

<h2>Create Project</h2>

<form method="post" action="../Controller/createProjectHandler.php" onsubmit="return validateProjectForm()">

<table>

<tr>
    <td>Project Name</td>
    <td>
        <input type="text" name="name" id="name" value="<?php echo $name; ?>" placeholder="Enter project name">
    </td>
    <td><p id="nameErr" style="color:red"><?php echo $nameErr; ?></p></td>
</tr>

<tr>
    <td>Description</td>
    <td>
        <textarea name="description" id="description" placeholder="Enter description"><?php echo $description; ?></textarea>
    </td>
    <td><p id="descriptionErr" style="color:red"><?php echo $descriptionErr; ?></p></td>
</tr>

<tr>
    <td>Deadline</td>
    <td>
        <input type="date" name="deadline" id="deadline" value="<?php echo $deadline; ?>">
    </td>
    <td><p style="color:red"><?php echo $deadlineErr; ?></p></td>
</tr>

<tr>
    <td>Color Label</td>
    <td>
        <label><input type="radio" name="color_label" value="#e74c3c" <?php if($color_label == "#e74c3c"){ echo "checked"; } ?>> Red</label><br>
        <label><input type="radio" name="color_label" value="#3498db" <?php if($color_label == "#3498db"){ echo "checked"; } ?>> Blue</label><br>
        <label><input type="radio" name="color_label" value="#2ecc71" <?php if($color_label == "#2ecc71"){ echo "checked"; } ?>> Green</label><br>
        <label><input type="radio" name="color_label" value="#f39c12" <?php if($color_label == "#f39c12"){ echo "checked"; } ?>> Orange</label><br>
        <label><input type="radio" name="color_label" value="#9b59b6" <?php if($color_label == "#9b59b6"){ echo "checked"; } ?>> Purple</label>
    </td>
    <td><p style="color:red"><?php echo $colorErr; ?></p></td>
</tr>

<tr>
    <td>Assign Members</td>
    <td>
        <?php

        if($members->num_rows > 0){

            while($row = $members->fetch_assoc()){

                $member_id = $row["id"];
                $member_name = $row["name"];

                echo "<input type='checkbox' name='members[]' value='$member_id'> $member_name <br>";
            }

        }else{
            echo "No Workspace Members Found";
        }

        ?>
    </td>
    <td><p style="color:red"><?php echo $memberErr; ?></p></td>
</tr>

<tr>
    <td></td>
    <td>
        <input type="submit" value="Create Project">
    </td>
</tr>

</table>

</form>

<br>

<a href="projectList.php">Back to Project List</a>

</body>
</html>