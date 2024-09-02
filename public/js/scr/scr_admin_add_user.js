const appendAlert = (message, type) =>{
    var placementFrom = 'top';
    var placementAlign = 'right';
    var state = 'danger';
    var style = 'plain';
    var content = {};

    content.message = message;
    content.title = "Etat d'enregistrement d'utilisateur !";
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
const createMessage = (userName, userGroupe)=>{
    return 'Utilisateur <strong>'+userName+ '</strong> a été ajouter comme '+userGroupe
}
//== Class definition
var SweetAlert2Demo = (function () {
  //== Demos
  var initDemos = function () 
      {
          $("#alert_demo_7").click(function (e) {
              swal({
                  title: "Confirmez-vous la saisie ?",
                  text: "You won't be able to revert this!",
                  type: "warning",
                  buttons: {
                      confirm: {
                          text: "Oui, valider!",
                          className: "btn btn-success",
                      },
                      cancel: {
                          text: "Non, annuler",
                          visible: true,
                          className: "btn btn-danger",
                      },
                  },
              }).then((Delete) => {
                  if (Delete) {
                      swal({
                          title: "Deleted!",
                          text: "Your file has been deleted.",
                          type: "success",
                          buttons: {
                              confirm: {
                                  className: "btn btn-success",
                              },
                          },
                      });
                  } else {
                      swal.close();
                  }
              });
          });
      };

      return {
          //== Init
          init: function () {
          initDemos();
          },
  };
})();


//== Class Initialization
jQuery(document).ready(function () {
  SweetAlert2Demo.init();
  $("#displayNotif").on("click", function (e) {
      e.preventDefault();
      var addUserForm = $('#add_user_form');
      console.log('Ajout de nouvelle utilisateur');
      var userMatricule = $('input[name="user_matricule"]').val();
      var idGroupe = $('select[name="id_groupe"]').val();  
      var groupeName = $('select[name="id_groupe"] option:selected').text()

      var user_data = {
          'user_matricule':userMatricule,
          'id_groupe':idGroupe
      };
      const xhr = new newXhr();
      xhr.onreadystatechange = function(){
          if (this.readyState == 4 && this.status == 200){
              const rep = this.response;
              // Ajout mesage d'erreur
              console.log(rep);
              if (rep.status == false){
                  appendAlert(rep.message, 'danger');
              }
              else if (rep.status == true){
                  console.log(rep);
                  appendAlert(createMessage(userMatricule,groupeName), 'success');
                //   window.location.href = rep.path; // to admin
              }
          }
          else if(this.readyState == 4){
              appendAlert('Erreur de traitement de requete','danger');
          }
      }
      xhr.open('POST', addUserForm.attr('action'), true)
      xhr.responseType = "json";
      xhr.send(JSON.stringify(user_data));
  });
});