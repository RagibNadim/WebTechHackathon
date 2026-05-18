<?php

session_start();

include "../Model/DatabaseConnection.php";

$project_id = $_GET["id"] ?? 0;
$workspace_id = $_SESSION["workspace_id"] ?? "";

if(!$workspace_id){
    Header("Location: ../../Noshin/View/login.php");
    exit();
}

if($project_id == 0){
    die("Invalid Project ID");
}

$db = new DatabaseConnection();
$connection = $db->openConnection();

$project = $db->getProjectById($connection, $project_id, $workspace_id);
$data = $project->fetch_assoc();

if(!$data){
    die("Project Not Found");
}

$summaryResult = $db->getTaskSummaryByProject($connection, $project_id);
$summary = $summaryResult->fetch_assoc();

$todo_count = $summary["todo_count"] ?? 0;
$progress_count = $summary["progress_count"] ?? 0;
$done_count = $summary["done_count"] ?? 0;

$members = $db->getProjectMembersWithTaskCount($connection, $project_id);

?>

<html>
<head>
    <title>Project Detail</title>
    <link rel="stylesheet" href="../CSS/style.css">
</head>

<body>

<h2>Project Detail</h2>

<table border="1">

<tr>
    <td><strong>Project Name</strong></td>
    <td><?php echo $data["name"]; ?></td>
</tr>

<tr>
    <td><strong>Description</strong></td>
    <td><?php echo $data["description"]; ?></td>
</tr>

<tr>
    <td><strong>Deadline</strong></td>
    <td><?php echo $data["deadline"]; ?></td>
</tr>

<tr>
    <td><strong>Color Label</strong></td>
    <td><?php echo $data["color_label"]; ?></td>
</tr>

</table>

<br>

<h3>Task Summary</h3>

<span class="badge todo">To Do: <?php echo $todo_count; ?></span>
<span class="badge progress">In Progress: <?php echo $progress_count; ?></span>
<span class="badge done">Done: <?php echo $done_count; ?></span>

<br><br>

<h3>Project Members</h3>

<table border="1">

<tr>
    <th>Name</th>
    <th>Email</th>
    <th>Assigned Task Count</th>
</tr>

<?php

if($members->num_rows > 0){

    while($row = $members->fetch_assoc()){

        $name = $row["name"];
        $email = $row["email"];
        $assigned_task_count = $row["assigned_task_count"];

        echo "<tr>
                <td>$name</td>
                <td>$email</td>
                <td>$assigned_task_count</td>
              </tr>";
    }

}else{
    echo "<tr>
            <td colspan='3'>No Members Found</td>
          </tr>";
}

?>

</table>

<br>

<a href="projectList.php">Back to Project List</a>

</body>
</html>