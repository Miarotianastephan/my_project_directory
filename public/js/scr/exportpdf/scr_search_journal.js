$(document).ready(function(){
    
    const appendAlert = (message, type) =>{
        var placementFrom = 'top';
        var placementAlign = 'right';
        var state = 'danger';
        var style = 'plain';
        var content = {};
    
        content.message = message;
        content.title = "TEST ENVOIE !";
        if (style == "withicon") {
          content.icon = "fa fa-bell";
        } else {
          content.icon = "none";
        }
    
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

    // 
    var formSearch = document.getElementById('formSearch');
    var actionSearch = formSearch.action;
    var searchResultBody = document.getElementById('searchResultBody');
    formSearch.addEventListener('submit', function(e){
        e.preventDefault();
        const data = new FormData(formSearch);
        // for (let [key, value] of datas.entries()) {
        //     console.log(`${key}: ${value}`);
        // }
        // console.log(actionSearch);
        const xhr = new newXhr();
        xhr.onreadystatechange = function(){
            if (this.readyState == 4 && this.status == 200){
                const rep = this.response;
                console.log(rep);
                if (rep.status == false){
                    appendAlert(rep.message, 'warning');
                }
                else if (rep.status == true){
                    appendAlert(rep.message, 'success');
                    searchResultBody.innerHTML = '';
                    var searchRow = '';
                    rep.search_result.forEach(mv => {
                      let isDebit = mv.IS_MVT_DEBIT;
                      let debitCell = isDebit=='1' ? mv.MVT_MONTANT : '';
                      let creditCell = isDebit=='0' ? mv.MVT_MONTANT : '';
                      searchRow += `
                        <tr>
                            <td>${mv.EVN_DATE_OPERATION}</td>
                            <td>${mv.CPT_NUMERO}</td>
                            <td>${mv.CPT_LIBELLE}</td>
                            <td>${debitCell}</td>
                            <td>${creditCell}</td>
                        </tr>
                      `;
                  });
                  searchResultBody.innerHTML = searchRow;
                }
            }
            else if(this.readyState == 4){
                appendAlert('Erreur de traitement de requete','danger');
            }
        }//Recupérer le path du controller via .action sur le formulaire
        xhr.open('POST', actionSearch, true)
        xhr.responseType = "json";
        xhr.send(data);
    })
})