
$(document).ready(function () {
    const appendAlert = (message, type) =>{
        var placementFrom = 'top';
        var placementAlign = 'right';
        var state = 'danger';
        var style = 'plain';
        var content = {};
    
        content.message = message;
        content.title = "Etat de modification de la demande !";
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
    // pour créer le select pour les comptes mères
    var createSelectForGroupUtilisateur = (data) =>{
        console.log({
            '1' : 'createSelectForGroupUtilisateur',
            '2': data
        })
        var options = '';
        // Bouclez sur le tableau et créez les options
        options += `<option value="-1">Ne pas changer</option>`;
        data.forEach(obj => {
            options += `<option value="${obj.id}">${obj.cptNumero}-${obj.cptLibelle}</option>`;
        });
        return options
    }

    // Variable nécessaire
    var pathToControllerGetGroups = $('#pathToFindCompteDepense').val();
    var allGroupUtilisateur = null;
    var selectedRow = null;
    var selectedCol = null;
    var bodyList = $('#bodyList');
    var formModalUpdate = document.querySelector('#formModalUpdate');
    // var formModalDelete = document.querySelector('#formModalDelete');

    // Pour avoir l'element mere => <tr>
    function getTrElement(theTargetElement){
        return theTargetElement.parentElement.parentElement.parentElement.parentElement;
    }
    // Avoir les valeurs dans les colonnes du selectedRow en Objet
    function getSelectedRowChildrenWithIds(){
        var selectedRowChildrenWithIds = [];
        const children = selectedRow.children
        for (var i = 0; i < children.length; i++) {
            if (children[i].id) {
                var tempElement = {
                    id: children[i].id,
                    label: children[i].innerText,
                    elem: children[i]
                };
                selectedRowChildrenWithIds.push(tempElement);
            }
        }
        return selectedRowChildrenWithIds;
    }
    // Pour avoir les données séléctionnées sous forme de dataMapping
    function convertArrayToMap(dataArray) {
        return dataArray.reduce((map, item) => {
            map[item.id] = item;
            return map;
        }, {});
    }
    function getAllColNames(){
        var colNames = [];
        const cols = document.querySelector('#cols');
        const children = cols.children;
        for (var i = 0; i < children.length; i++) {
            if (children[i].id == "isCol") {
                colNames.push(children[i].innerText);
            }
        }
        console.log("Nom des colonnes=> " + colNames)
        return colNames;
    }
    var options_for_selected_row_html = null;
    function getAllPlanCompteOptions(pathToControllerGetGroup){
        // Asynchrone with xhr
        const xhr = new newXhr();
        xhr.onreadystatechange = function(){
            if (this.readyState == 4 && this.status == 200){
                allGroupUtilisateur = JSON.parse(xhr.responseText);
                options_for_selected_row_html = createSelectForGroupUtilisateur(allGroupUtilisateur);
                console.log(options_for_selected_row_html);
            }
            else if(this.readyState == 4){
                appendAlert('Erreur de traitement de requete','danger');
            }
        }
        xhr.open('GET', pathToControllerGetGroup, false) // false pour que attendre la fin de l'opération
        xhr.send();
    }
    function setSelectedRow(tr){
        const idOfTrElement = tr.id;
        selectedRow = document.getElementById(idOfTrElement);
        selectedCol = getSelectedRowChildrenWithIds();
        selectedCol = convertArrayToMap(selectedCol);
        console.log(selectedCol);
    }

    // ---xxx--
    bodyList.on('click', (e) =>{
        target = e.target;
        const tr = getTrElement(target);
        if(tr.localName == "tr"){
            setSelectedRow(tr);
            // usr_grp_id = selectedCol['usr_grp_id'].label;
            getAllPlanCompteOptions(pathToControllerGetGroups);
            if( target.classList.contains('update') ){
                var colNames = getAllColNames();
                console.log(colNames)
                var sectionUpdate = document.querySelector('#sectionUpdate');
                sectionUpdate.innerHTML = ''
                // les ID des colonnes : 
                    // 0: demande_date, 1:demande_  ref, 2:demande_libelle
                // --------------------
                sectionUpdate.innerHTML = 
                `
                <input name="id_demande_fonds" value="${selectedRow.id}" type="hidden" readonly />
                <div class="row gy-4 mb-3">
                    <div class="col-md-6"> 
                        <label for="id-1" class="form-label">${colNames[0]} </label>
                        <input type="text" id="id-1" class="form-control form-control" name="${selectedCol['demande_date'].id}" value="${selectedCol['demande_date'].label}" />
                    </div>
                    <div class="col-md-6">
                        <label for="id-2" class="form-label">${colNames[1]}</label>
                        <input type="text" id="id-2" class="form-control bg-primary-subtle form-control" name="${selectedCol['demande_ref'].id}_old" value="${selectedCol['demande_ref'].label}" readonly/>
                    </div>
                </div> 
                <div class="row gy-4 mb-3">
                    <div class="col-md-12">
                        <label for="id-3" class="form-label">${colNames[2]}</label>
                        <input type="text" id="id-3" class="form-control bg-primary-subtle form-control" name="${selectedCol['demande_libelle'].id}" value="${selectedCol['demande_libelle'].label}" readonly/>
                    </div>
                </div> 
                <div class="row gy-4 mb-3">
                    <div class="col-md-6">
                        <label for="id-3" class="form-label">${colNames[3]}</label>
                        <input type="text" id="id-3" class="form-control bg-primary-subtle form-control" name="${selectedCol['demande_montant'].id}" value="${selectedCol['demande_montant'].label}" readonly/>
                    </div>
                    <div class="col-md-6">
                        <label for="id-3" class="form-label">Nouveau ${colNames[3]}</label>
                        <input type="text" id="id-3" class="form-control bg-primary-subtle form-control" name="demande_montant_nouveau" placeholder="Saisir un nouveau montant" value="non"/>
                    </div>
                </div>  
                <div class="row gy-4 mb-3">
                    <div class="col-md-12">
                        <label for="selectOption" class="text-danger">Nouveau compte d'attribution</label>
                        <select name="id_compte_depense" class="form-select" id="selectOption">
                            ${options_for_selected_row_html}
                        </select>
                    </div>
                </div>
                `
            }
        }
    });

    // SUBMIT UPDATE
    formModalUpdate.addEventListener('submit', (e)=> {
        e.preventDefault();
        const formUp = new FormData(formModalUpdate);
        for (let [key, value] of formUp.entries()) {
            console.log(`${key}: ${value}`);
        }
        console.log(selectedCol);
        // Asynchrone with xhr
        const xhr = new newXhr();
        xhr.onreadystatechange = function(){
            if (this.readyState == 4 && this.status == 200){
                const rep = this.response;
                console.log(rep);
                if (rep.status == false){
                    appendAlert(rep.message, 'danger');
                }
                else if (rep.status == true){
                    if(rep.update == true){
                        appendAlert(rep.message, 'success');
                        selectedCol['demande_date'].elem.innerText = formUp.get('demande_date');
                        selectedCol['demande_ref'].elem.innerText = formUp.get('demande_ref_old');
                        if(formUp.get('demande_montant') != "non"){
                            selectedCol['demande_montant'].elem.innerText = formUp.get('demande_montant_nouveau');
                        }
                        if(formUp.get('id_compte_depense') != -1){
                            selectedCol['demande_libelle'].elem.innerText = $('#selectOption').find("option:selected").text();
                        }
                    }else if(rep.update == false){
                        appendAlert(rep.message, 'warning');
                    }
                }
            }
            else if(this.readyState == 4){
                appendAlert('Erreur de traitement de requete','danger');
            }
        }//Recupérer le path du controller via .action sur le formulaire
        xhr.open('POST', formModalUpdate.action, true)
        xhr.responseType = "json";
        xhr.send(formUp);
    });


  });