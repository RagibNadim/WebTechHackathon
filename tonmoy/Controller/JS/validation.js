function validateTaskForm(){
    var title = document.getElementById('modal_title').value.trim();
    var assignee = document.getElementById('modal_assignee').value;
    var due = document.getElementById('modal_due_date').value;
    var priorityEls = document.getElementsByName('priority');

    var ok = true;

    document.getElementById('modal_title_err').innerText = '';
    document.getElementById('modal_assignee_err').innerText = '';
    document.getElementById('modal_priority_err').innerText = '';
    document.getElementById('modal_due_err').innerText = '';

    if(title === ''){
        document.getElementById('modal_title_err').innerText = 'Title is required';
        ok = false;
    }

    if(assignee === ''){
        document.getElementById('modal_assignee_err').innerText = 'Select member';
        ok = false;
    }

    var hasPriority = false;
    for(var i=0;i<priorityEls.length;i++){
        if(priorityEls[i].checked){ hasPriority = true; break; }
    }
    if(!hasPriority){
        document.getElementById('modal_priority_err').innerText = 'Select priority';
        ok = false;
    }

    if(due === ''){
        document.getElementById('modal_due_err').innerText = 'Select due date';
        ok = false;
    }

    return ok;
}
