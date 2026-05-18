<?php
ini_set('display_errors', 0);
error_reporting(0);
session_start();

require_once __DIR__ . '/../Model/DatabaseConnection.php';

$project_id = isset($_GET['project_id']) ? intval($_GET['project_id']) : (isset($_SESSION['project_id']) ? intval($_SESSION['project_id']) : 1);

$conn = db_connect();

$todo = getTasksByStatus($conn, $project_id, 'todo');
$inprogress = getTasksByStatus($conn, $project_id, 'in-progress');
$done = getTasksByStatus($conn, $project_id, 'done');
$users_result = getAllUsers($conn);
$users = array();
if($users_result){
    while($u = mysqli_fetch_assoc($users_result)){
        $users[$u['id']] = $u['name'];
    }
}
if(empty($users)){
    $users = [0 => 'Unassigned'];
}

$title_val = $_SESSION['task_title'] ?? '';
$desc_val = $_SESSION['task_description'] ?? '';
$due_val = $_SESSION['task_due_date'] ?? '';
$assignee_val = $_SESSION['task_assignee'] ?? '';
$priority_val = $_SESSION['task_priority'] ?? '';

function get_initials($name){
    $name = trim($name);
    if($name === '') return '';
    $parts = explode(' ', $name);
    $letters = '';
    foreach($parts as $p){
        if($p !== '') $letters .= mb_substr($p,0,1);
        if(strlen($letters) >= 2) break;
    }
    return strtoupper($letters);
}

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Task Board</title>
    <link rel="stylesheet" href="/tanmoyproject/WebTechHackathon/Tonmoy/CSS/style.css">
</head>
<body>

<div class="header">
    <h2>Task Board</h2>
    <div>
        <button id="newTaskBtn">+ New Task</button>
    </div>
</div>

<?php if(!empty($_SESSION['message'])): ?>
    <div id="message"><?php echo htmlspecialchars($_SESSION['message']); unset($_SESSION['message']); ?></div>
<?php else: ?>
    <div id="message" style="display:none"></div>
<?php endif; ?>

<div id="taskModal" class="modal" style="display:none">
    <div class="modal-content">
        <h3>New Task</h3>
        <form method="post" action="/tanmoyproject/WebTechHackathon/Tonmoy/Controller/createTaskHandler.php" onsubmit="return validateTaskForm();" enctype="multipart/form-data">
            <input type="hidden" name="project_id" value="<?php echo $project_id; ?>">
            <label>Title
                <input type="text" id="modal_title" name="title" value="<?php echo htmlspecialchars($title_val); ?>">
                <div id="modal_title_err" class="error"><?php echo $_SESSION['titleErr'] ?? ''; ?></div>
            </label>
            <label>Description
                <textarea id="modal_description" name="description"><?php echo htmlspecialchars($desc_val); ?></textarea>
            </label>
            <label>Assignee
                <select id="modal_assignee" name="assignee_id">
                    <option value="">-- Select --</option>
                    <?php if(empty($users)): ?>
                        <option value="" disabled>No users available</option>
                    <?php else: ?>
                        <?php foreach($users as $mid => $mname): ?>
                            <option value="<?php echo $mid; ?>" <?php echo ($assignee_val == $mid) ? 'selected' : ''; ?>><?php echo htmlspecialchars($mname); ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
                <div id="modal_assignee_err" class="error"><?php echo $_SESSION['assigneeErr'] ?? ''; ?></div>
            </label>
            <label>Priority
                <div>
                    <label><input type="radio" name="priority" value="low" <?php echo ($priority_val=='low')?'checked':''; ?>> Low</label>
                    <label><input type="radio" name="priority" value="medium" <?php echo ($priority_val=='medium')?'checked':''; ?>> Medium</label>
                    <label><input type="radio" name="priority" value="high" <?php echo ($priority_val=='high')?'checked':''; ?>> High</label>
                </div>
                <div id="modal_priority_err" class="error"><?php echo $_SESSION['priorityErr'] ?? ''; ?></div>
            </label>
            <label>Due Date
                <input type="date" id="modal_due_date" name="due_date" value="<?php echo htmlspecialchars($due_val); ?>">
                <div id="modal_due_err" class="error"><?php echo $_SESSION['dueErr'] ?? ''; ?></div>
            </label>
            <label>Attachment (optional)
                <input type="file" name="attachment">
            </label>
            <div class="form-actions" style="margin-top:8px">
                <button type="submit">Create</button>
                <button type="button" id="modalClose">Cancel</button>
            </div>
        </form>
    </div>
</div>

<div class="board">

    <div class="column" data-status="todo">
        <h3>To Do</h3>

        <?php while($r = $todo->fetch_assoc()): ?>
            <?php
                $taskId = (int)$r['id'];
                $assignee = '';
                if(!empty($r['assignee_id']) && isset($users[$r['assignee_id']])){
                    $assignee = $users[$r['assignee_id']];
                } elseif(!empty($r['assignee_id'])){
                    $stmtName = mysqli_prepare($conn, "SELECT name FROM users WHERE id = ?");
                    if($stmtName){
                        mysqli_stmt_bind_param($stmtName, 'i', $r['assignee_id']);
                        mysqli_stmt_execute($stmtName);
                        $resName = mysqli_stmt_get_result($stmtName);
                        if($rowName = mysqli_fetch_assoc($resName)) $assignee = $rowName['name'];
                        mysqli_stmt_close($stmtName);
                    }
                }
                $initials = htmlspecialchars(get_initials($assignee));
                $title = htmlspecialchars($r['title']);
                $priority_raw = strtolower(trim($r['priority'] ?? ''));
                if($priority_raw === '') $priority_raw = 'low';
                if(!in_array($priority_raw, ['low','medium','high'])) $priority_raw = 'low';
                $priority = htmlspecialchars(ucfirst($priority_raw));
                $due = htmlspecialchars($r['due_date'] ?? '');
            ?>

            <div class="card" id="taskCard<?php echo $taskId; ?>" data-task-id="<?php echo $taskId; ?>" data-due-date="<?php echo $due; ?>" data-status="todo">
                <div class="card-top">
                    <div class="card-title"><?php echo $title; ?></div>
                    <div class="card-badge"><?php echo $initials; ?></div>
                </div>
                <div class="card-meta">
                    <div class="card-priority <?php echo 'priority-' . $priority_raw; ?>"><?php echo $priority; ?></div>
                    <div class="card-due"><?php echo $due; ?></div>
                </div>
                <div class="card-actions">
                    <button class="arrow move-left" data-task-id="<?php echo $taskId; ?>" data-new-status="todo" disabled>←</button>
                    <button class="arrow move-right" data-task-id="<?php echo $taskId; ?>" data-new-status="in-progress">→</button>
                </div>
            </div>

        <?php endwhile; ?>
    </div>

    <div class="column" data-status="in-progress">
        <h3>In Progress</h3>

        <?php while($r = $inprogress->fetch_assoc()): ?>
            <?php
                $taskId = (int)$r['id'];
                $assignee = '';
                if(!empty($r['assignee_id']) && isset($users[$r['assignee_id']])){
                    $assignee = $users[$r['assignee_id']];
                } elseif(!empty($r['assignee_id'])){
                    $stmtName = mysqli_prepare($conn, "SELECT name FROM users WHERE id = ?");
                    if($stmtName){
                        mysqli_stmt_bind_param($stmtName, 'i', $r['assignee_id']);
                        mysqli_stmt_execute($stmtName);
                        $resName = mysqli_stmt_get_result($stmtName);
                        if($rowName = mysqli_fetch_assoc($resName)) $assignee = $rowName['name'];
                        mysqli_stmt_close($stmtName);
                    }
                }
                $initials = htmlspecialchars(get_initials($assignee));
                $title = htmlspecialchars($r['title']);
                $priority_raw = strtolower(trim($r['priority'] ?? ''));
                if($priority_raw === '') $priority_raw = 'low';
                if(!in_array($priority_raw, ['low','medium','high'])) $priority_raw = 'low';
                $priority = htmlspecialchars(ucfirst($priority_raw));
                $due = htmlspecialchars($r['due_date'] ?? '');
            ?>

            <div class="card" id="taskCard<?php echo $taskId; ?>" data-task-id="<?php echo $taskId; ?>" data-due-date="<?php echo $due; ?>" data-status="in-progress">
                <div class="card-top">
                    <div class="card-title"><?php echo $title; ?></div>
                    <div class="card-badge"><?php echo $initials; ?></div>
                </div>
                <div class="card-meta">
                    <div class="card-priority <?php echo 'priority-' . $priority_raw; ?>"><?php echo $priority; ?></div>
                    <div class="card-due"><?php echo $due; ?></div>
                </div>
                <div class="card-actions">
                    <button class="arrow move-left" data-task-id="<?php echo $taskId; ?>" data-new-status="todo">←</button>
                    <button class="arrow move-right" data-task-id="<?php echo $taskId; ?>" data-new-status="done">→</button>
                </div>
            </div>

        <?php endwhile; ?>
    </div>

    <div class="column" data-status="done">
        <h3>Done</h3>

        <?php while($r = $done->fetch_assoc()): ?>
            <?php
                $taskId = (int)$r['id'];
                $assignee = '';
                if(!empty($r['assignee_id']) && isset($users[$r['assignee_id']])){
                    $assignee = $users[$r['assignee_id']];
                } elseif(!empty($r['assignee_id'])){
                    $stmtName = mysqli_prepare($conn, "SELECT name FROM users WHERE id = ?");
                    if($stmtName){
                        mysqli_stmt_bind_param($stmtName, 'i', $r['assignee_id']);
                        mysqli_stmt_execute($stmtName);
                        $resName = mysqli_stmt_get_result($stmtName);
                        if($rowName = mysqli_fetch_assoc($resName)) $assignee = $rowName['name'];
                        mysqli_stmt_close($stmtName);
                    }
                }
                $initials = htmlspecialchars(get_initials($assignee));
                $title = htmlspecialchars($r['title']);
                $priority_raw = strtolower(trim($r['priority'] ?? ''));
                if($priority_raw === '') $priority_raw = 'low';
                if(!in_array($priority_raw, ['low','medium','high'])) $priority_raw = 'low';
                $priority = htmlspecialchars(ucfirst($priority_raw));
                $due = htmlspecialchars($r['due_date'] ?? '');
            ?>

            <div class="card" id="taskCard<?php echo $taskId; ?>" data-task-id="<?php echo $taskId; ?>" data-due-date="<?php echo $due; ?>" data-status="done">
                <div class="card-top">
                    <div class="card-title"><?php echo $title; ?></div>
                    <div class="card-badge"><?php echo $initials; ?></div>
                </div>
                <div class="card-meta">
                    <div class="card-priority <?php echo 'priority-' . $priority_raw; ?>"><?php echo $priority; ?></div>
                    <div class="card-due"><?php echo $due; ?></div>
                </div>
                <div class="card-actions">
                    <button class="arrow move-left" data-task-id="<?php echo $taskId; ?>" data-new-status="in-progress">←</button>
                    <button class="arrow move-right" data-task-id="<?php echo $taskId; ?>" data-new-status="done" disabled>→</button>
                </div>
            </div>

        <?php endwhile; ?>
    </div>

</div>

<script src="/tanmoyproject/WebTechHackathon/Tonmoy/Controller/JS/validation.js"></script>
<script src="/tanmoyproject/WebTechHackathon/Tonmoy/Controller/JS/ajax.js"></script>
<script>

var newBtn = document.getElementById('newTaskBtn');
if(newBtn){
    newBtn.addEventListener('click', function(){
        var modal = document.getElementById('taskModal'); if(modal) modal.style.display = 'block';
    });
}
var modalCloseBtn = document.getElementById('modalClose');
if(modalCloseBtn){
    modalCloseBtn.addEventListener('click', function(){
        var modal = document.getElementById('taskModal'); if(modal) modal.style.display = 'none';
    });
}

window.addEventListener('click', function(e){
    var modal = document.getElementById('taskModal');
    if(modal && e.target === modal){ modal.style.display = 'none'; }
});


if(<?php echo (!empty($_SESSION['titleErr']) || !empty($_SESSION['assigneeErr']) || !empty($_SESSION['priorityErr']) || !empty($_SESSION['dueErr'])) ? 'true' : 'false'; ?>){
    var m = document.getElementById('taskModal'); if(m) m.style.display = 'block';
}
<?php unset($_SESSION['titleErr'], $_SESSION['assigneeErr'], $_SESSION['priorityErr'], $_SESSION['dueErr']); ?>

document.addEventListener('DOMContentLoaded', function(){
    if(typeof initDragAndDrop === 'function') initDragAndDrop();
    if(typeof initArrowButtons === 'function') initArrowButtons();
});

document.addEventListener('DOMContentLoaded', function(){
    try{
        var cards = document.querySelectorAll('.card[data-due-date]');
        for(var i=0;i<cards.length;i++){
            var card = cards[i];
            if(typeof window.evaluateCardOverdue === 'function'){
                window.evaluateCardOverdue(card);
            }
        }
    }catch(e){ console.error('Overdue highlight init error', e); }
});
</script>

</body>
</html>
