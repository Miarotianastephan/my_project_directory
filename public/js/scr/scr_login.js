var loginForm = document.querySelector('#login_form');
loginForm.addEventListener('submit', (e) => {
    e.preventDefault();

    var user_matricule = loginForm.querySelector('#form_user_matricule').value;
    var user_password = loginForm.querySelector('#form_user_pass').value;

    var user_data = {
        'user_matricule':user_matricule,
        'user_pass':user_password
    };

    const xhr = new newXhr();
    xhr.onreadystatechange = function(){

        if (this.readyState == 4 && this.status == 200){
            const rep = this.response;
            // Ajout mesage d'erreur
            console.log(rep);
            if (rep.valeur == false){
                appendAlert('Matricule incorrect ou introuvable !', 'danger');
            }
            else if (rep.valeur == true){
                console.log(rep);
                window.location.href = rep.path; // to admin
            }
        }
        else if(this.readyState == 4){
            alert('Erreur de traitement de requete');
        }
        
    }
    xhr.open('POST', loginForm.action, true)
    xhr.responseType = "json";
    xhr.send(JSON.stringify(user_data));
})

// Message d'erreur
const alertPlaceholder = document.getElementById('liveAlertPlaceholder')
const appendAlert = (message, type) => {
  const wrapper = document.createElement('div')
  wrapper.innerHTML = [
    `<div class="alert alert-${type} alert-dismissible" role="alert">`,
    `   <div>${message}</div>`,
    '   <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>',
    '</div>'
  ].join('')

  alertPlaceholder.append(wrapper)
}