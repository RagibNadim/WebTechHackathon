<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
if(!ob_get_level()) ob_start();

require_once __DIR__ . '/../Model/DatabaseConnection.php';

$baseUrl = '/WebTechHackathon/Tonmoy';

if(($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST'){
	if(ob_get_level()) ob_end_clean();
	header('Location: ../View/taskBoard.php');
	exit();
}

$project_id = $_POST['project_id'] ?? $_SESSION['project_id'] ?? 1;
$title = trim($_POST['title'] ?? '');
$description = trim($_POST['description'] ?? '');
$assignee_id = $_POST['assignee_id'] ?? '';
$priority = $_POST['priority'] ?? '';
$due_date = $_POST['due_date'] ?? '';

$_SESSION['task_title'] = $title;
$_SESSION['task_description'] = $description;
$_SESSION['task_due_date'] = $due_date;
$_SESSION['task_assignee'] = $assignee_id;
$_SESSION['task_priority'] = $priority;

$hasError = false;

if($title == ''){
	$_SESSION['titleErr'] = 'Title is required';
	$hasError = true;
} else { unset($_SESSION['titleErr']); }

$assignee_id = ($assignee_id === '' || $assignee_id === '0') ? null : $assignee_id;
unset($_SESSION['assigneeErr']);

if($priority == ''){
	$_SESSION['priorityErr'] = 'Select priority';
	$hasError = true;
} else { unset($_SESSION['priorityErr']); }

if($due_date == ''){
	$_SESSION['dueErr'] = 'Select due date';
	$hasError = true;
} else { unset($_SESSION['dueErr']); }


if($hasError){
	if(ob_get_level()) ob_end_clean();
	header('Location: ../View/taskBoard.php');
	exit();
}

$conn = db_connect();

$user_id = $_SESSION['user_id'] ?? 1;
$status = 'todo';

if($assignee_id === null){
	$assignee_id = $user_id;
}

$task_id = createTask($conn, $project_id, $title, $description, $assignee_id, $priority, $due_date, $status);

if($task_id){
	addActivityLog($conn, $task_id, $user_id, 'created task', date('Y-m-d H:i:s'));

	unset($_SESSION['task_title'], $_SESSION['task_description'], $_SESSION['task_due_date'], $_SESSION['task_assignee'], $_SESSION['task_priority']);

	$_SESSION['message'] = 'Task created successfully';
	if(ob_get_level()) ob_end_clean();
	header('Location: ../View/taskBoard.php');
	exit();
} else {
	$_SESSION['titleErr'] = 'Failed to create task';
	if(ob_get_level()) ob_end_clean();
	header('Location: ../View/taskBoard.php');
	exit();
}

?>
