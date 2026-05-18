<?php

session_start();

include "../Model/DatabaseConnection.php";

$workspace_id = $_SESSION["workspace_id"] ?? 1;

$db = new DatabaseConnection();

$connection = $db->openConnection();

$projects = $db->getArchivedProjects($connection, $workspace_id);

?>

<html>

<head>

    <title>Archived Projects</title>

    <link rel="stylesheet" href="../CSS/style.css">

</head>

<body>

<h2>Archived Projects</h2>

<br>

<a href="projectList.php">Back to Project List</a>

<br><br>

<table border="1">

<tr>

    <th>ID</th>

    <th>Project Name</th>

    <th>Description</th>

    <th>Deadline</th>

    <th>Color Label</th>

</tr>

<?php

if($projects->num_rows > 0){

    while($row = $projects->fetch_assoc()){

        $project_id = $row["id"];

        $name = $row["name"];

        $description = $row["description"];

        $deadline = $row["deadline"];

        $color_label = $row["color_label"];

        echo "

        <tr>

            <td>$project_id</td>

            <td>$name</td>

            <td>$description</td>

            <td>$deadline</td>

            <td>$color_label</td>

        </tr>

        ";
    }

}else{

    echo "

    <tr>

        <td colspan='5'>
            No Archived Projects Found
        </td>

    </tr>

    ";
}

?>

</table>

</body>

</html>