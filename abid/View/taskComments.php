<link rel="stylesheet" href="../CSS/style.css">
<script src="../Controller/JS/validation.js"></script>
<script src="../Controller/JS/ajax.js"></script>

<div class="task-details-panel">
    <h3>Task Comments</h3>
    
    <div id="comments-list-<?php echo $task_id; ?>" class="comments-container">
        <?php if(!empty($existing_comments)): ?>
            <?php foreach($existing_comments as $comment): ?>
                <div id="comment-row-<?php echo $comment['id']; ?>" class="comment-item">
                    <div class="comment-header">
                        <strong><?php echo htmlspecialchars($comment['user_name']); ?></strong> 
                        <span><?php echo $comment['created_at']; ?></span>
                    </div>
                    <div class="comment-body">
                        <?php echo htmlspecialchars($comment['body']); ?>
                    </div>
                    <?php if($comment['user_id'] == $_SESSION['user_id']): ?>
                        <a href="#" class="delete-btn" onclick="deleteComment(event, <?php echo $comment['id']; ?>, <?php echo $project_id; ?>)">Delete</a>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <form onsubmit="postComment(event, <?php echo $task_id; ?>, <?php echo $project_id; ?>)">
        <textarea id="comment-body-<?php echo $task_id; ?>" placeholder="Write a comment..." rows="3"></textarea>
        <span id="comment-error-<?php echo $task_id; ?>" style="display:block; margin-bottom: 5px;"></span>
        <button type="submit">Post Comment</button>
    </form>
</div>