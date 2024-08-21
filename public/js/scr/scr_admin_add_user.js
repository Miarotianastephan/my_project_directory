// var addUserForm = $("#add_user_form");
$("#displayNotif").on("click", function (e) {
    e.preventDefault();
    console.log('')
    // var userMatricule = $('input[name="user_matricule"]').val();
    // var idGroupe = $('select[name="id_groupe"]').val();  
    // var user_data = {
    //     'user_matricule':userMatricule,
    //     'id_groupe':idGroupe
    // };
    // const xhr = new newXhr();
    // xhr.onreadystatechange = function(){
    //     if (this.readyState == 4 && this.status == 200){
    //         const rep = this.response;
    //         // Ajout mesage d'erreur
    //         console.log(rep);
    //         if (rep.status == false){
    //             appendAlert('Matricule incorrect ou introuvable !', 'danger');
    //         }
    //         else if (rep.status == true){
    //             console.log(rep);
    //             window.location.href = rep.path; // to admin
    //         }
    //     }
    //     else if(this.readyState == 4){
    //         alert('Erreur de traitement de requete');
    //     }
    // }
    // xhr.open('POST', addUserForm.action, true)
    // xhr.responseType = "json";
    // xhr.send(JSON.stringify(user_data));
    appendAlert('L \'utilisateur 90000 a été ajouter avec succès ', 'danger');
});

const appendAlert = (message, type) =>{
    var placementFrom = 'top';
    var placementAlign = 'right';
    var state = 'danger';
    var style = 'plain';
    var content = {};

    content.message = message;
    content.title = "Enregistrement de nouvelle utilisateur !";
    if (style == "withicon") {
      content.icon = "fa fa-bell";
    } else {
      content.icon = "none";
    }
    // content.url = "index.html";
    // content.target = "_blank";

    $.notify(content, {
      type: type,
      placement: {
        from: placementFrom,
        align: placementAlign,
      },
      time: 1000,
      delay: 0,
    });
}