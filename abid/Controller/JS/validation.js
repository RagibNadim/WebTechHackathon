// Validates the comment form before submitting
function validateComment(taskId) {
    const bodyInput = document.getElementById(`comment-body-${taskId}`);
    const errorSpan = document.getElementById(`comment-error-${taskId}`);
    
    if (bodyInput.value.trim() === '') {
        errorSpan.innerText = 'Comment cannot be empty.';
        errorSpan.style.color = 'red';
        return false;
    }
    
    errorSpan.innerText = '';
    return true;
}