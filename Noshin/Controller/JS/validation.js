function validateRegistration(){

    let name = document.getElementById("name").value;
    let email = document.getElementById("email").value;
    let password = document.getElementById("password").value;

    document.getElementById("nameErr").innerHTML = "";
    document.getElementById("emailErr").innerHTML = "";
    document.getElementById("passwordErr").innerHTML = "";

    let isValid = true;

    if(name == ""){
        document.getElementById("nameErr").innerHTML = "Name is required";
        isValid = false;
    }

    if(email == ""){
        document.getElementById("emailErr").innerHTML = "Email is required";
        isValid = false;
    }

    if(password == ""){
        document.getElementById("passwordErr").innerHTML = "Password is required";
        isValid = false;
    }
    else if(password.length < 8){
        document.getElementById("passwordErr").innerHTML = "Password must be at least 8 characters";
        isValid = false;
    }

    return isValid;
}

function validateLogin(){

    let email = document.getElementById("email").value;
    let password = document.getElementById("password").value;

    document.getElementById("emailErr").innerHTML = "";
    document.getElementById("passwordErr").innerHTML = "";

    let isValid = true;

    if(email == ""){
        document.getElementById("emailErr").innerHTML = "Email is required";
        isValid = false;
    }

    if(password == ""){
        document.getElementById("passwordErr").innerHTML = "Password is required";
        isValid = false;
    }

    return isValid;
}

function validateWorkspace(){

    let name = document.getElementById("name").value;
    let description = document.getElementById("description").value;

    document.getElementById("workspaceNameErr").innerHTML = "";
    document.getElementById("workspaceDescriptionErr").innerHTML = "";

    let isValid = true;

    if(name == ""){
        document.getElementById("workspaceNameErr").innerHTML = "Workspace name is required";
        isValid = false;
    }

    if(description == ""){
        document.getElementById("workspaceDescriptionErr").innerHTML = "Workspace description is required";
        isValid = false;
    }

    return isValid;
}

function validateJoinWorkspace(){

    let invite_code = document.getElementById("invite_code").value;

    document.getElementById("joinErr").innerHTML = "";

    if(invite_code == ""){
        document.getElementById("joinErr").innerHTML = "Invite code is required";
        return false;
    }

    return true;
}