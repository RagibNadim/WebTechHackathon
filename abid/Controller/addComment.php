<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["error" => "Unauthorized"]);
    exit;
}

require_once '../Model/DatabaseConnection.php';
require_once '../../config/helpers.php'; // Path assumes config is in the project root

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = new DatabaseConnection();
    $conn = $db->connect();

    $task_id = $_POST['task_id'] ?? null;
    $project_id = $_POST['project_id'] ?? null; 
    $user_id = $_SESSION['user_id'];
    $body = htmlspecialchars($_POST['body'] ?? ''); 

    if (empty($body) || !$task_id) {
        echo json_encode(["error" => "Comment body cannot be empty"]);
        exit;
    }

    $comment_id = $db->addComment($task_id, $user_id, $body);

    if ($comment_id) {
        $task = $db->getTaskTitle($task_id);
        $action_text = "Commented on task '{$task['title']}'";
        log_activity($project_id, $user_id, $action_text);

        echo json_encode([
            "id" => $comment_id,
            "user_name" => $_SESSION['name'] ?? 'You',
            "body" => $body,
            "timestamp" => "Just now"
        ]);
    } else {
        echo json_encode(["error" => "Failed to add comment"]);
    }
}
?>