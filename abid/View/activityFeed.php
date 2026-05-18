<link rel="stylesheet" href="../CSS/style.css">
<script src="../Controller/JS/ajax.js"></script>

<div class="activity-page">
    <h2>Project Activity Feed</h2>
    
    <div style="margin-bottom: 15px;">
        <label for="member-filter">Filter by Member:</label>
        <select id="member-filter" onchange="loadActivityFeed(<?php echo $project_id; ?>)" style="padding: 5px;">
            <option value="">All Members</option>
            <?php if(!empty($project_members)): ?>
                <?php foreach($project_members as $member): ?>
                    <option value="<?php echo $member['id']; ?>"><?php echo htmlspecialchars($member['name']); ?></option>
                <?php endforeach; ?>
            <?php endif; ?>
        </select>
    </div>

    <ul id="activity-feed-list" style="padding: 0;">
        </ul>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        loadActivityFeed(<?php echo $project_id; ?>);
    });
</script>