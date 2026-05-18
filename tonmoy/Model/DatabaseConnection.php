<?php

// Procedural DB functions for Tonmoy task board

function db_connect(){
    $db_host = "localhost";
    $db_username = "root";
    $db_password = "";
    $db_name = "task_project_management";

    $conn = mysqli_connect($db_host, $db_username, $db_password, $db_name);
    if(!$conn){
        die('Database connection error: ' . mysqli_connect_error());
    }
    // set charset
    mysqli_set_charset($conn, 'utf8mb4');
    return $conn;
}

function createTask($conn, $project_id, $title, $description, $assignee_id, $priority, $due_date, $status){
    $sql = "INSERT INTO tasks(project_id, title, description, assigned_to, priority, due_date, status) VALUES(?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    if(!$stmt) return false;
    $ai = (is_null($assignee_id) ? 0 : (int)$assignee_id);
    mysqli_stmt_bind_param($stmt, 'ississs', $project_id, $title, $description, $ai, $priority, $due_date, $status);
    $res = mysqli_stmt_execute($stmt);
    if($res){
        $id = mysqli_insert_id($conn);
        mysqli_stmt_close($stmt);
        return $id;
    }
    mysqli_stmt_close($stmt);
    return false;
}

function getTasksByStatus($conn, $project_id, $status){
    $sql = "SELECT id, project_id, title, description, assigned_to AS assignee_id, status, priority, due_date FROM tasks WHERE project_id = ? AND status = ? ORDER BY id DESC";
    $stmt = mysqli_prepare($conn, $sql);
    if(!$stmt) return false;
    mysqli_stmt_bind_param($stmt, 'is', $project_id, $status);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);
    return $result;
}

function getTaskById($conn, $task_id){
    $sql = "SELECT id, project_id, title, description, assigned_to AS assignee_id, status, priority, due_date FROM tasks WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    if(!$stmt) return false;
    mysqli_stmt_bind_param($stmt, 'i', $task_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);
    return $result;
}

function updateTaskStatus($conn, $task_id, $status){
    $sql = "UPDATE tasks SET status = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    if(!$stmt) return false;
    mysqli_stmt_bind_param($stmt, 'si', $status, $task_id);
    $res = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    return $res;
}

function getProjectMembers($conn, $project_id){
    $sql = "SELECT users.id, users.name, users.email
            FROM project_members
            INNER JOIN users ON project_members.user_id = users.id
            WHERE project_members.project_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    if(!$stmt) return false;
    mysqli_stmt_bind_param($stmt, 'i', $project_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);
    return $result;
}

function getAllUsers($conn){
    $sql = "SELECT id, name FROM users ORDER BY name ASC";
    $stmt = mysqli_prepare($conn, $sql);
    if(!$stmt) return false;
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);
    return $result;
}

function addActivityLog($conn, $task_id, $user_id, $action, $created_at){
    $cols = [];
    $res = mysqli_query($conn, "SHOW COLUMNS FROM activity_logs");
    if($res){
        while($row = mysqli_fetch_assoc($res)){
            $cols[] = $row['Field'];
        }
        mysqli_free_result($res);
    }

    $taskCandidates = ['task_id','task','object_id','related_id','item_id','reference_id'];
    $userCandidates = ['user_id','user','actor_id','actor'];
    $actionCandidates = ['action','description','note','message','details'];
    $createdCandidates = ['created_at','created','timestamp','time','created_on','createdAt'];

    $insertCols = [];
    $params = [];
    $types = '';

    foreach($taskCandidates as $c){ if(in_array($c, $cols)){ $insertCols[] = $c; $params[] = ($task_id===null?null:(int)$task_id); $types .= 'i'; break; } }
    $project_id_val = null;
    if(in_array('project_id', $cols)){
        if($task_id !== null){
            $q = mysqli_prepare($conn, "SELECT project_id FROM tasks WHERE id = ?");
            if($q){
                mysqli_stmt_bind_param($q, 'i', $task_id);
                mysqli_stmt_execute($q);
                $r = mysqli_stmt_get_result($q);
                if($r && ($row = mysqli_fetch_assoc($r))){
                    $project_id_val = (int)$row['project_id'];
                }
                mysqli_stmt_close($q);
            }
        }
        if($project_id_val !== null){
            array_unshift($insertCols, 'project_id');
            array_unshift($params, $project_id_val);
            $types = 'i' . $types;
        } else {
            return true;
        }
    }
    foreach($userCandidates as $c){ if(in_array($c, $cols)){ $insertCols[] = $c; $params[] = (int)$user_id; $types .= 'i'; break; } }
    foreach($actionCandidates as $c){ if(in_array($c, $cols)){ $insertCols[] = $c; $params[] = $action; $types .= 's'; break; } }
    foreach($createdCandidates as $c){ if(in_array($c, $cols)){ $insertCols[] = $c; $params[] = $created_at; $types .= 's'; break; } }

    if(empty($insertCols)) return false;

    $placeholders = implode(', ', array_fill(0, count($insertCols), '?'));
    $sql = "INSERT INTO activity_logs(" . implode(', ', $insertCols) . ") VALUES(" . $placeholders . ")";
    $stmt = mysqli_prepare($conn, $sql);
    if(!$stmt) return false;

    if($types !== ''){
        $bind_names = [];
        $bind_names[] = & $types;
        for($i=0;$i<count($params);$i++){
            $bind_names[] = & $params[$i];
        }
        call_user_func_array('mysqli_stmt_bind_param', array_merge([$stmt], $bind_names));
    }

    $res = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    return $res;
}

?>
