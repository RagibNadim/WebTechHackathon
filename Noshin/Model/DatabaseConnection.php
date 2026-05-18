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

    function createUser($connection, $name, $email, $password_hash){
        $sql = "INSERT INTO users(name, email, password_hash) VALUES(?, ?, ?)";

        $statement = $connection->prepare($sql);
        $statement->bind_param("sss", $name, $email, $password_hash);

        return $statement->execute();
    }

    function getUserByEmail($connection, $email){
        $sql = "SELECT * FROM users WHERE email = ?";

        $statement = $connection->prepare($sql);
        $statement->bind_param("s", $email);
        $statement->execute();

        return $statement->get_result();
    }

    function getFirstWorkspaceByUserId($connection, $user_id){
        $sql = "SELECT workspaces.*
                FROM workspace_members
                INNER JOIN workspaces ON workspace_members.workspace_id = workspaces.id
                WHERE workspace_members.user_id = ?
                ORDER BY workspace_members.joined_at ASC
                LIMIT 1";

        $statement = $connection->prepare($sql);
        $statement->bind_param("i", $user_id);
        $statement->execute();

        return $statement->get_result();
    }

    function createWorkspace($connection, $name, $description, $owner_id, $invite_code){
        $sql = "INSERT INTO workspaces(name, description, owner_id, invite_code)
                VALUES(?, ?, ?, ?)";

        $statement = $connection->prepare($sql);
        $statement->bind_param("ssis", $name, $description, $owner_id, $invite_code);

        if($statement->execute()){
            return $connection->insert_id;
        }

        return false;
    }

    function addWorkspaceMember($connection, $workspace_id, $user_id){
        $sql = "INSERT INTO workspace_members(workspace_id, user_id)
                VALUES(?, ?)";

        $statement = $connection->prepare($sql);
        $statement->bind_param("ii", $workspace_id, $user_id);

        return $statement->execute();
    }

    function getWorkspaceByInviteCode($connection, $invite_code){
        $sql = "SELECT * FROM workspaces WHERE invite_code = ?";

        $statement = $connection->prepare($sql);
        $statement->bind_param("s", $invite_code);
        $statement->execute();

        return $statement->get_result();
    }

    function checkWorkspaceMembership($connection, $workspace_id, $user_id){
        $sql = "SELECT * FROM workspace_members
                WHERE workspace_id = ? AND user_id = ?";

        $statement = $connection->prepare($sql);
        $statement->bind_param("ii", $workspace_id, $user_id);
        $statement->execute();

        return $statement->get_result();
    }

    function getUserWorkspaces($connection, $user_id){
        $sql = "SELECT workspaces.*
                FROM workspace_members
                INNER JOIN workspaces ON workspace_members.workspace_id = workspaces.id
                WHERE workspace_members.user_id = ?";

        $statement = $connection->prepare($sql);
        $statement->bind_param("i", $user_id);
        $statement->execute();

        return $statement->get_result();
    }

    function getWorkspaceById($connection, $workspace_id){
        $sql = "SELECT * FROM workspaces WHERE id = ?";

        $statement = $connection->prepare($sql);
        $statement->bind_param("i", $workspace_id);
        $statement->execute();

        return $statement->get_result();
    }

    function getWorkspaceMembers($connection, $workspace_id){
        $sql = "SELECT workspace_members.id as member_id,
                       workspace_members.joined_at,
                       users.id as user_id,
                       users.name,
                       users.email
                FROM workspace_members
                INNER JOIN users ON workspace_members.user_id = users.id
                WHERE workspace_members.workspace_id = ?";

        $statement = $connection->prepare($sql);
        $statement->bind_param("i", $workspace_id);
        $statement->execute();

        return $statement->get_result();
    }

    function isWorkspaceOwner($connection, $workspace_id, $user_id){
        $sql = "SELECT * FROM workspaces
                WHERE id = ? AND owner_id = ?";

        $statement = $connection->prepare($sql);
        $statement->bind_param("ii", $workspace_id, $user_id);
        $statement->execute();

        return $statement->get_result();
    }

    function getWorkspaceMemberById($connection, $member_id){
        $sql = "SELECT * FROM workspace_members WHERE id = ?";

        $statement = $connection->prepare($sql);
        $statement->bind_param("i", $member_id);
        $statement->execute();

        return $statement->get_result();
    }

    function removeWorkspaceMember($connection, $member_id){
        $sql = "DELETE FROM workspace_members WHERE id = ?";

        $statement = $connection->prepare($sql);
        $statement->bind_param("i", $member_id);

        return $statement->execute();
    }
}

?>