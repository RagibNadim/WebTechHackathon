<?php
class DatabaseConnection {
    private $host = '127.0.0.1';
    private $db_name = 'task_project_management';
    private $username = 'root'; // Update if your local DB uses a different user
    private $password = '';     // Update if your local DB has a password
    public $conn;

    public function connect() {
        $this->conn = null;
        try {
            $this->conn = new PDO('mysql:host=' . $this->host . ';dbname=' . $this->db_name, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            echo 'Connection Error: ' . $e->getMessage();
        }
        return $this->conn;
    }

    public function addComment($task_id, $user_id, $body) {
        $query = "INSERT INTO comments (task_id, user_id, body) VALUES (:task_id, :user_id, :body)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':task_id', $task_id);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':body', $body);
        if ($stmt->execute()) {
            return $this->conn->lastInsertId(); 
        }
        return false;
    }

    public function deleteComment($comment_id, $user_id) {
        $query = "DELETE FROM comments WHERE id = :id AND user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $comment_id);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    public function getActivityFeed($project_id, $filter_user_id = null) {
        $query = "SELECT a.*, u.name FROM activity_logs a
                  JOIN users u ON a.user_id = u.id
                  WHERE a.project_id = :project_id";
        if ($filter_user_id != null) {
            $query .= " AND a.user_id = :filter_user_id";
        }
        $query .= " ORDER BY a.created_at DESC LIMIT 50";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':project_id', $project_id);
        if ($filter_user_id != null) {
            $stmt->bindParam(':filter_user_id', $filter_user_id);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTaskTitle($task_id) {
        $query = "SELECT title FROM tasks WHERE id = :task_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':task_id', $task_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>