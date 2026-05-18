function removeMember(member_id){

    let xhttp = new XMLHttpRequest();

    xhttp.onreadystatechange = function(){

        if(this.readyState == 4 && this.status == 200){

            let response = JSON.parse(this.responseText);

            document.getElementById("message").innerHTML = response.message;

            if(response.ok){

                let row = document.getElementById("memberRow" + member_id);

                if(row){
                    row.style.display = "none";
                }
            }
        }
    };

    xhttp.open("POST", "../Controller/removeWorkspaceMember.php", true);

    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

    xhttp.send("member_id=" + member_id);
}