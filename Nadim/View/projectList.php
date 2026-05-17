<?php

session_start();

include "../Model/DatabaseConnection.php";

$workspace_id = $_SESSION["workspace_id"] ?? 1;

$theme = $_COOKIE["theme"] ?? "Default";

$db = new DatabaseConnection();

$connection = $db->openConnection();

$projects = $db->getProjectsByWorkspace($connection, $workspace_id);

?>

<html>

<head>

    <title>Project List</title>

    <link rel="stylesheet" href="../CSS/style.css">

    <script src="../Controller/JS/ajax.js"></script>

</head>

<body>

<h2>Project List</h2>

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

<a href="createProject.php">Create Project</a>

<br><br>

<a href="archivedProjects.php">Archived Projects</a>

<br><br>

<p id="message" style="color:green;"></p>

<table border="1">

<tr>

    <th>ID</th>

    <th>Project Name</th>

    <th>Description</th>

    <th>Deadline</th>

    <th>Color Label</th>

    <th>Progress</th>

    <th>Action</th>

</tr>

<?php

if($projects->num_rows > 0){

    while($row = $projects->fetch_assoc()){

        $project_id = $row["id"];

        $name = $row["name"];

        $description = $row["description"];

        $deadline = $row["deadline"];

        $color_label = $row["color_label"];

        $progress_result = $db->getProjectProgress($connection, $project_id);

        $progress_data = $progress_result->fetch_assoc();

        $total_tasks = $progress_data["total_tasks"] ?? 0;

        $completed_tasks = $progress_data["completed_tasks"] ?? 0;

        $percentage = 0;

        if($total_tasks > 0){

            $percentage = ($completed_tasks / $total_tasks) * 100;
        }

        echo "

        <tr id='projectRow$project_id'>

            <td>$project_id</td>

            <td>$name</td>

            <td>$description</td>

            <td>$deadline</td>

            <td>$color_label</td>

            <td>".number_format($percentage, 2)."%</td>

            <td>

                <a href='projectDetail.php?id=$project_id'>Details</a>

                |

                <a href='projectSettings.php?id=$project_id'>Settings</a>

                |

                <button onclick='archiveProject($project_id)' type='button'>
                    Archive
                </button>

            </td>

        </tr>

        ";
    }

}else{

    echo "

    <tr>

        <td colspan='7'>
            No Projects Found
        </td>

    </tr>

    ";
}

?>

</table>

<br>

<a href="../Controller/logout.php">Logout</a>

</body>

</html>