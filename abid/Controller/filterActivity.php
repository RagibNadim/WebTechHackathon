<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["error" => "Unauthorized"]);
    exit;
}

require_once '../Model/DatabaseConnection.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $project_id = $_GET['project_id'] ?? null;
    $filter_user_id = !empty($_GET['user_id']) ? $_GET['user_id'] : null;

    if (!$project_id) {
        echo json_encode(["error" => "Project ID is required"]);
        exit;
    }

    $db = new DatabaseConnection();
    $db->connect();
    $logs = $db->getActivityFeed($project_id, $filter_user_id); 
    
    echo json_encode($logs);
}
?>