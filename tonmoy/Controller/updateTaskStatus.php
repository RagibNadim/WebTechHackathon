<?php
ini_set('display_errors', 0);
error_reporting(0);
session_start();

header('Content-Type: application/json');

require_once __DIR__ . '/../Model/DatabaseConnection.php';
$task_id = isset($_POST['task_id']) ? intval($_POST['task_id']) : 0;
$new_status = isset($_POST['status']) ? trim($_POST['status']) : '';

if($task_id <= 0 || $new_status == ''){
	echo json_encode(['ok' => false, 'message' => 'Invalid input']);
	exit();
}

$conn = db_connect();

$sql = "SELECT status FROM tasks WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 'i', $task_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
if($row = mysqli_fetch_assoc($result)){
	$current = $row['status'];
} else {
	echo json_encode(['ok' => false, 'message' => 'Task not found']);
	exit();
}

$allowed = [
	'todo' => ['in-progress'],
	'in-progress' => ['todo','done'],
	'done' => ['in-progress']
];

if(!isset($allowed[$current]) || !in_array($new_status, $allowed[$current])){
	echo json_encode(['ok' => false, 'message' => 'Invalid status transition']);
	exit();
}

$updated = updateTaskStatus($conn, $task_id, $new_status);

if($updated){
	$user_id = $_SESSION['user_id'] ?? 1;
	addActivityLog($conn, $task_id, $user_id, 'status changed to ' . $new_status, date('Y-m-d H:i:s'));
	echo json_encode(['ok' => true, 'new_status' => $new_status]);
	exit();
} else {
	echo json_encode(['ok' => false, 'message' => 'Failed to update status']);
	exit();
}

?>
