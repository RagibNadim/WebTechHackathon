function postComment(event, taskId, projectId) {
    event.preventDefault();
    if(!validateComment(taskId)) return; // Client-side validation

    const bodyInput = document.getElementById(`comment-body-${taskId}`);
    const body = bodyInput.value;

    const formData = new FormData();
    formData.append('task_id', taskId);
    formData.append('project_id', projectId);
    formData.append('body', body);

    fetch('../Controller/addComment.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            alert(data.error);
        } else {
            const commentsList = document.getElementById(`comments-list-${taskId}`);
            const newComment = document.createElement('div');
            newComment.id = `comment-row-${data.id}`;
            newComment.className = 'comment-item new-comment';
            newComment.innerHTML = `
                <div class="comment-header">
                    <strong>${data.user_name}</strong> <span>${data.timestamp}</span>
                </div>
                <div class="comment-body">${data.body}</div>
                <a href="#" class="delete-btn" onclick="deleteComment(event, ${data.id}, ${projectId})">Delete</a>
            `;
            commentsList.appendChild(newComment);
            bodyInput.value = ''; 
            document.getElementById(`comment-error-${taskId}`).innerText = '';
        }
    })
    .catch(error => console.error('Error:', error));
}

function deleteComment(event, commentId, projectId) {
    event.preventDefault();
    if (!confirm('Are you sure you want to delete this comment?')) return;

    fetch(`../Controller/deleteComment.php`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `id=${commentId}&project_id=${projectId}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.ok) {
            const commentElement = document.getElementById(`comment-row-${commentId}`);
            commentElement.style.opacity = 0;
            setTimeout(() => commentElement.remove(), 500);
        } else {
            alert(data.error || 'Failed to delete');
        }
    })
    .catch(error => console.error('Error:', error));
}

function loadActivityFeed(projectId) {
    const userIdFilter = document.getElementById('member-filter').value;
    let url = `../Controller/filterActivity.php?project_id=${projectId}`;
    if (userIdFilter) url += `&user_id=${userIdFilter}`;

    fetch(url)
    .then(response => response.json())
    .then(data => {
        const feedContainer = document.getElementById('activity-feed-list');
        feedContainer.innerHTML = ''; 

        if (data.error || data.length === 0) {
            feedContainer.innerHTML = `<li class="no-activity">${data.error || 'No recent activity.'}</li>`;
            return;
        }

        data.forEach(log => {
            const li = document.createElement('li');
            li.className = 'activity-item';
            li.innerHTML = `<strong>${log.name}</strong> ${log.action_text} <span class="time">(${log.created_at})</span>`;
            feedContainer.appendChild(li);
        });
    })
    .catch(error => console.error('Error:', error));
}