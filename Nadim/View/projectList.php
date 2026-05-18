<?php

session_start();

include "../Model/DatabaseConnection.php";

$workspace_id = $_SESSION["workspace_id"] ?? "";

if(!$workspace_id){
    Header("Location: ../../Noshin/View/login.php");
    exit();
}

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
|
<a href="archivedProjects.php">Archived Projects</a>
|
<a href="../Controller/logout.php">Logout</a>
|
<a href="../../Noshin/View/dashboard.php">Workspace Dashboard</a>
|
<a href="../../tonmoy/View/taskBoard.php">Task Board</a>
|
<a href="../../abid/View/activityFeed.php">Activity Feed</a>

<br><br>

<p id="message" style="color:green;"></p>

<div class="project-grid">

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

        $deadlineClass = "";

        if($deadline < date("Y-m-d")){
            $deadlineClass = "overdue";
        }

        echo "<div class='project-card' id='projectRow$project_id' style='border-left: 8px solid $color_label;'>";

        echo "<h3>$name</h3>";
        echo "<p>$description</p>";
        echo "<p class='$deadlineClass'>Deadline: $deadline</p>";

        echo "<div class='member-initials'>";

        $projectMembers = $db->getProjectMembers($connection, $project_id);

        while($member = $projectMembers->fetch_assoc()){
            $memberName = $member["name"];
            $initial = strtoupper(substr($memberName, 0, 1));

            echo "<span>$initial</span>";
        }

        echo "</div>";

        if($total_tasks == 0){
            echo "<p>No tasks yet</p>";
        }else{
            echo "<p>Progress: ".number_format($percentage, 2)."%</p>";
            echo "<div class='progress-bar'>
                    <div class='progress-fill' style='width: ".number_format($percentage, 2)."%'></div>
                  </div>";
        }

        echo "<br>";

        echo "<a href='projectDetail.php?id=$project_id'>Details</a> | ";
        echo "<a href='projectSettings.php?id=$project_id'>Settings</a> | ";
        echo "<button type='button' onclick='archiveProject($project_id)'>Archive</button>";

        echo "</div>";
    }

}else{
    echo "<p>No Projects Found</p>";
}

?>

</div>

</body>
</html>