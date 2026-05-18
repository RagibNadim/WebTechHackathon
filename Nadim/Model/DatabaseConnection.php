<?php

class DatabaseConnection{

    function openConnection(){
        $db_host = "localhost";
        $db_username = "root";
        $db_password = "";
        $db_name = "task_project_management";

        $connection = new mysqli($db_host, $db_username, $db_password, $db_name);

        if($connection->connect_error){
            die("Could not connect to the database. Original Error ".$connection->connect_error);
        }

        return $connection;
    }

    function getWorkspaceMembers($connection, $workspace_id){
        $sql = "SELECT users.id, users.name, users.email
                FROM workspace_members
                INNER JOIN users ON workspace_members.user_id = users.id
                WHERE workspace_members.workspace_id = ?";

        $statement = $connection->prepare($sql);
        $statement->bind_param("i", $workspace_id);
        $statement->execute();

        return $statement->get_result();
    }

    function createProject($connection, $workspace_id, $name, $description, $deadline, $color_label){
        $sql = "INSERT INTO projects(workspace_id, name, description, deadline, color_label, is_archived)
                VALUES(?, ?, ?, ?, ?, 0)";

        $statement = $connection->prepare($sql);
        $statement->bind_param("issss", $workspace_id, $name, $description, $deadline, $color_label);

        if($statement->execute()){
            return $connection->insert_id;
        }

        return false;
    }

    function addProjectMember($connection, $project_id, $user_id){
        $sql = "INSERT INTO project_members(project_id, user_id)
                VALUES(?, ?)";

        $statement = $connection->prepare($sql);
        $statement->bind_param("ii", $project_id, $user_id);

        return $statement->execute();
    }

    function getProjectsByWorkspace($connection, $workspace_id){
        $sql = "SELECT * FROM projects
                WHERE workspace_id = ? AND is_archived = 0
                ORDER BY created_at DESC";

        $statement = $connection->prepare($sql);
        $statement->bind_param("i", $workspace_id);
        $statement->execute();

        return $statement->get_result();
    }

    function getArchivedProjects($connection, $workspace_id){
        $sql = "SELECT * FROM projects
                WHERE workspace_id = ? AND is_archived = 1
                ORDER BY created_at DESC";

        $statement = $connection->prepare($sql);
        $statement->bind_param("i", $workspace_id);
        $statement->execute();

        return $statement->get_result();
    }

    function getProjectById($connection, $project_id, $workspace_id){
        $sql = "SELECT * FROM projects
                WHERE id = ? AND workspace_id = ?";

        $statement = $connection->prepare($sql);
        $statement->bind_param("ii", $project_id, $workspace_id);
        $statement->execute();

        return $statement->get_result();
    }

    function updateProject($connection, $project_id, $name, $description, $deadline, $color_label){
        $sql = "UPDATE projects
                SET name = ?, description = ?, deadline = ?, color_label = ?
                WHERE id = ?";

        $statement = $connection->prepare($sql);
        $statement->bind_param("ssssi", $name, $description, $deadline, $color_label, $project_id);

        return $statement->execute();
    }

    function removeProjectMembers($connection, $project_id){
        $sql = "DELETE FROM project_members WHERE project_id = ?";

        $statement = $connection->prepare($sql);
        $statement->bind_param("i", $project_id);

        return $statement->execute();
    }

    function archiveProject($connection, $project_id){
        $sql = "UPDATE projects SET is_archived = 1 WHERE id = ?";

        $statement = $connection->prepare($sql);
        $statement->bind_param("i", $project_id);

        return $statement->execute();
    }

    function getProjectProgress($connection, $project_id){
        $sql = "SELECT
                COUNT(*) as total_tasks,
                SUM(CASE WHEN status = 'done' THEN 1 ELSE 0 END) as completed_tasks
                FROM tasks
                WHERE project_id = ?";

        $statement = $connection->prepare($sql);
        $statement->bind_param("i", $project_id);
        $statement->execute();

        return $statement->get_result();
    }

    function getProjectMembers($connection, $project_id){
        $sql = "SELECT users.id, users.name, users.email
                FROM project_members
                INNER JOIN users ON project_members.user_id = users.id
                WHERE project_members.project_id = ?";

        $statement = $connection->prepare($sql);
        $statement->bind_param("i", $project_id);
        $statement->execute();

        return $statement->get_result();
    }

    function getProjectMemberIds($connection, $project_id){
        $sql = "SELECT user_id FROM project_members WHERE project_id = ?";

        $statement = $connection->prepare($sql);
        $statement->bind_param("i", $project_id);
        $statement->execute();

        return $statement->get_result();
    }

    function getTaskSummaryByProject($connection, $project_id){
        $sql = "SELECT
                SUM(CASE WHEN status = 'todo' THEN 1 ELSE 0 END) as todo_count,
                SUM(CASE WHEN status = 'in-progress' THEN 1 ELSE 0 END) as progress_count,
                SUM(CASE WHEN status = 'done' THEN 1 ELSE 0 END) as done_count
                FROM tasks
                WHERE project_id = ?";

        $statement = $connection->prepare($sql);
        $statement->bind_param("i", $project_id);
        $statement->execute();

        return $statement->get_result();
    }

    function getProjectMembersWithTaskCount($connection, $project_id){
        $sql = "SELECT users.id, users.name, users.email,
                COUNT(tasks.id) as assigned_task_count
                FROM project_members
                INNER JOIN users ON project_members.user_id = users.id
                LEFT JOIN tasks ON tasks.assigned_to = users.id AND tasks.project_id = project_members.project_id
                WHERE project_members.project_id = ?
                GROUP BY users.id, users.name, users.email";

        $statement = $connection->prepare($sql);
        $statement->bind_param("i", $project_id);
        $statement->execute();

        return $statement->get_result();
    }
}

?>