<?php

session_start();

include "../Model/DatabaseConnection.php";

$project_id = $_GET["id"] ?? 0;

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

$progress_result = $db->getProjectProgress($connection, $project_id);

$progress_data = $progress_result->fetch_assoc();

$total_tasks = $progress_data["total_tasks"] ?? 0;

$completed_tasks = $progress_data["completed_tasks"] ?? 0;

$percentage = 0;

if($total_tasks > 0){
    $percentage = ($completed_tasks / $total_tasks) * 100;
}

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

<tr>

    <td><strong>Created At</strong></td>

    <td><?php echo $data["created_at"]; ?></td>

</tr>

<tr>

    <td><strong>Total Tasks</strong></td>

    <td><?php echo $total_tasks; ?></td>

</tr>

<tr>

    <td><strong>Completed Tasks</strong></td>

    <td><?php echo $completed_tasks; ?></td>

</tr>

<tr>

    <td><strong>Project Progress</strong></td>

    <td>

        <?php echo number_format($percentage, 2); ?>%

    </td>

</tr>

</table>

<br>

<a href="projectList.php">Back to Project List</a>

</body>

</html>