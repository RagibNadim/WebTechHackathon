function validateProjectForm(){

    let name = document.getElementById("name").value;
    let description = document.getElementById("description").value;

    document.getElementById("nameErr").innerHTML = "";
    document.getElementById("descriptionErr").innerHTML = "";

    let isValid = true;

    if(name == ""){
        document.getElementById("nameErr").innerHTML = "Project name is required";
        isValid = false;
    }

    if(description == ""){
        document.getElementById("descriptionErr").innerHTML = "Description is required";
        isValid = false;
    }

    return isValid;
}