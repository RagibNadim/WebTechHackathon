<?php
// Shared helper required for all students to log activities [cite: 92]
function log_activity($project_id, $user_id, $action_text) {
    // Adjust path based on where config is relative to DatabaseConnection
    require_once __DIR__ . '/../ABID/Model/DatabaseConnection.php';
    $db = new DatabaseConnection();
    $conn = $db->connect();

    $query = "INSERT INTO activity_logs (project_id, user_id, action_text) VALUES (:project_id, :user_id, :action_text)";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':project_id', $project_id);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':action_text', $action_text);
    
    return $stmt->execute();
}
?>