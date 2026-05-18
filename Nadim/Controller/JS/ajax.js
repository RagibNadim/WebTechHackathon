function archiveProject(project_id){

    let xhttp = new XMLHttpRequest();

    xhttp.onreadystatechange = function(){

        if(this.readyState == 4 && this.status == 200){

            let response = JSON.parse(this.responseText);

            if(response.ok){

                document.getElementById("message").innerHTML = response.message;

                let row = document.getElementById("projectRow" + project_id);

                if(row){
                    row.style.display = "none";
                }

            }else{

                document.getElementById("message").innerHTML = response.message;
            }
        }
    };

    xhttp.open("POST", "../Controller/archiveProject.php", true);

    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

    xhttp.send("id=" + project_id);
}