
function updateStatusAjax(taskId, status, callback) {
    var body = 'task_id=' + encodeURIComponent(taskId) + '&status=' + encodeURIComponent(status);
    fetch('/tanmoyproject/WebTechHackathon/Tonmoy/Controller/updateTaskStatus.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: body
    }).then(function (response) {
        return response.json();
    }).then(function (json) {
        callback(null, json);
    }).catch(function (err) {
        callback(err);
    });
}

// Drag-and-drop helpers
function initDragAndDrop() {
    var cards = document.querySelectorAll('.card');
    cards.forEach(function (card) {
        card.setAttribute('draggable', 'true');
        card.addEventListener('dragstart', function (e) {
            e.dataTransfer.setData('text/plain', card.id);
            // small visual cue
            card.classList.add('dragging');
        });

        card.addEventListener('dragend', function (e) {
            card.classList.remove('dragging');
        });
    });

    var columns = document.querySelectorAll('.column');
    columns.forEach(function (col) {
        col.addEventListener('dragover', function (e) {
            e.preventDefault();
        });

        col.addEventListener('drop', function (e) {
            e.preventDefault();
            var data = e.dataTransfer.getData('text/plain');
            if (!data) return;


            var el = document.getElementById(data);
            var newStatus = col.getAttribute('data-status') || '';
            if (!el || !newStatus) return;


            var id = el.getAttribute('data-task-id') || el.id.replace(/[^0-9]/g, '');
            var params = 'task_id=' + encodeURIComponent(id) + '&status=' + encodeURIComponent(newStatus);
            updateStatusAjax(id, newStatus, function (err, res) {
                if (err) {
                    var msg = document.getElementById('message'); if (msg) msg.innerText = 'Network error';
                    return;
                }
                if (res && res.ok) {
                    el.setAttribute('data-status', res.new_status);
                    col.appendChild(el);
                    if (typeof window.evaluateCardOverdue === 'function') window.evaluateCardOverdue(el);
                } else {
                    var msg = document.getElementById('message'); if (msg) msg.innerText = res.message || 'Update failed';
                }
            });
        });
    });
}


window.initDragAndDrop = initDragAndDrop;


function initArrowButtons() {
    var lefts = document.querySelectorAll('.move-left');
    lefts.forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            var id = btn.getAttribute('data-task-id');
            var newStatus = btn.getAttribute('data-new-status');
            if (!id || !newStatus) return;
            updateStatusAjax(id, newStatus, function (err, res) {
                if (err) { var msg = document.getElementById('message'); if (msg) msg.innerText = 'Network error'; return; }
                if (res && res.ok) {
                    var card = document.getElementById('taskCard' + id);
                    if (card) {
                        card.setAttribute('data-status', res.new_status);
                        var target = document.querySelector('.column[data-status="' + res.new_status + '"]');
                        if (target) target.appendChild(card);
                        updateCardButtons(card, res.new_status);
                        if (typeof window.evaluateCardOverdue === 'function') window.evaluateCardOverdue(card);
                    }
                } else { var msg = document.getElementById('message'); if (msg) msg.innerText = res.message || 'Update failed'; }
            });
        });
    });

    var rights = document.querySelectorAll('.move-right');
    rights.forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            var id = btn.getAttribute('data-task-id');
            var newStatus = btn.getAttribute('data-new-status');
            if (!id || !newStatus) return;
            updateStatusAjax(id, newStatus, function (err, res) {
                if (err) { var msg = document.getElementById('message'); if (msg) msg.innerText = 'Network error'; return; }
                if (res && res.ok) {
                    var card = document.getElementById('taskCard' + id);
                    if (card) {
                        card.setAttribute('data-status', res.new_status);
                        var target = document.querySelector('.column[data-status="' + res.new_status + '"]');
                        if (target) target.appendChild(card);
                        updateCardButtons(card, res.new_status);
                    }
                } else { var msg = document.getElementById('message'); if (msg) msg.innerText = res.message || 'Update failed'; }
            });
        });
    });
}

function updateCardButtons(card, status) {
    var left = card.querySelector('.move-left');
    var right = card.querySelector('.move-right');
    if (!left || !right) return;
    if (status === 'todo') {
        left.disabled = true; left.setAttribute('data-new-status', 'todo');
        right.disabled = false; right.setAttribute('data-new-status', 'in-progress');
    } else if (status === 'in-progress') {
        left.disabled = false; left.setAttribute('data-new-status', 'todo');
        right.disabled = false; right.setAttribute('data-new-status', 'done');
    } else if (status === 'done') {
        left.disabled = false; left.setAttribute('data-new-status', 'in-progress');
        right.disabled = true; right.setAttribute('data-new-status', 'done');
    }
}

window.initArrowButtons = initArrowButtons;

function evaluateCardOverdue(card) {
    if (!card) return;
    try {
        var due = card.getAttribute('data-due-date');
        var status = (card.getAttribute('data-status') || '').toLowerCase();
        if (status === 'done') {
            card.classList.remove('overdue-task');
            card.classList.remove('border-red-500');
            return;
        }
        if (!due) return;
        var parts = due.split('-');
        if (parts.length !== 3) return;
        var year = parseInt(parts[0], 10);
        var month = parseInt(parts[1], 10) - 1;
        var day = parseInt(parts[2], 10);
        if (isNaN(year) || isNaN(month) || isNaN(day)) return;
        var dueDate = new Date(year, month, day);
        var today = new Date(); today.setHours(0, 0, 0, 0);
        dueDate.setHours(0, 0, 0, 0);
        if (dueDate < today) {
            card.classList.add('overdue-task');
            card.classList.add('border-red-500');
        } else {
            card.classList.remove('overdue-task');
            card.classList.remove('border-red-500');
        }
    } catch (e) { console.error('evaluateCardOverdue error', e); }
}

window.evaluateCardOverdue = evaluateCardOverdue;
