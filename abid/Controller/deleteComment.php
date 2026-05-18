<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["error" => "Unauthorized"]);
    exit;
}

require_once '../Model/DatabaseConnection.php';
require_once '../../config/helpers.php';

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    parse_str(file_get_contents("php://input"), $delete_vars);
    $comment_id = $delete_vars['id'] ?? null;
    $project_id = $delete_vars['project_id'] ?? null;
    $user_id = $_SESSION['user_id'];

    if (!$comment_id) {
        echo json_encode(["error" => "No comment ID provided"]);
        exit;
    }

    $db = new DatabaseConnection();
    $db->connect();

    if ($db->deleteComment($comment_id, $user_id)) {
        log_activity($project_id, $user_id, "Deleted a comment");
        echo json_encode(["ok" => true]);
    } else {
        echo json_encode(["error" => "Failed to delete comment"]);
    }
}
?>