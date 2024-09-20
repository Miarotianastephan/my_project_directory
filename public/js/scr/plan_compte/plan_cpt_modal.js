
$(document).ready(function () {
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
            options += `<option value="${obj.cpt_numero}">${obj.cpt_numero} ${obj.cpt_libelle}</option>`;
        });
        return options
    }

    // Variable nécessaire
    var pathToControllerGetGroups = $('#getAllCptMere').val();
    var allGroupUtilisateur = null;
    var selectedRow = null;
    var selectedCol = null;
    var bodyList = $('#bodyList');
    var formModalUpdate = document.querySelector('#formModalUpdate');
    // var formModalDelete = document.querySelector('#formModalDelete');

    // Pour avoir l'element mere => <tr>
    function getTrElement(theTargetElement){
        return theTargetElement.parentElement.parentElement;
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
    function getAllGroupOptions(pathToControllerGetGroup){
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
            getAllGroupOptions(pathToControllerGetGroups);
            if( target.classList.contains('update') ){
                var colNames = getAllColNames();
                var sectionUpdate = document.querySelector('#sectionUpdate');
                sectionUpdate.innerHTML = ''
                // les ID des colonnes : 
                    // 0: cpt_numero, 1:cpt_libelle, 2:cpt_libelle_mere
                // --------------------
                sectionUpdate.innerHTML = 
                `
                <input name="plan_cpt_id" value="${selectedRow.id}" type="hidden" readonly />
                <div class="row gy-4 mb-3">
                    <div class="col-md-12"> 
                        <label for="id-1" class="form-label">${colNames[0]} </label>
                        <input type="text" id="id-1" class="form-control form-control-sm" name="${selectedCol['cpt_numero'].id}" value="${selectedCol['cpt_numero'].label}" />
                    </div>
                </div> 
                <div class="row gy-4 mb-3">
                    <div class="col-md-12">
                        <label for="id-2" class="form-label">${colNames[1]}</label>
                        <input type="text" id="id-2" class="form-control bg-primary-subtle form-control-sm" name="${selectedCol['cpt_libelle'].id}_old" value="${selectedCol['cpt_libelle'].label}"/>
                    </div>
                </div> 
                <div class="row gy-4 mb-3">
                    <div class="col-md-12">
                        <label for="id-3" class="form-label">${colNames[2]}</label>
                        <input type="text" id="id-3" class="form-control bg-primary-subtle form-control-sm" name="${selectedCol['cpt_libelle_mere'].id}" value="${selectedCol['cpt_libelle_mere'].label}" readonly/>
                    </div>
                </div> 
                <div class="row gy-4 mb-3">
                    <div class="col-md-12">
                        <label for="selectOption" class="text-danger">Nouveau compte d'attribution</label>
                        <select name="compte_mere" class="form-select" id="selectOption">
                            ${options_for_selected_row_html}
                        </select>
                    </div>
                </div>
                `
            }
            if( target.classList.contains('delete') ){
                console.log("delete selected row");
                var deleteMessage = document.querySelector('#deleteMessage');
                deleteMessage.innerHTML = '';
                deleteMessage.innerHTML = 
                `Voulez-vous supprimer ${selectedCol['usr_matricule'].label}`;

                var sectionDelete = document.querySelector('#sectionDelete');
                sectionDelete.innerHTML = '';
                sectionDelete.innerHTML = 
                `<input name="id_prod" value="${selectedRow.id}" type="hidden" readonly />`

            }
        }
    })

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
                        selectedCol['cpt_numero'].elem.innerText = formUp.get('cpt_numero');
                        selectedCol['cpt_libelle'].elem.innerText = formUp.get('cpt_libelle_old');
                        selectedCol['cpt_libelle_mere'].elem.innerText = $('#selectOption').find("option:selected").text();;
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
    })
    // SUBMIT DELETE
    // formModalDelete.addEventListener('submit', (e)=> {
    //     e.preventDefault();
    //     var data = new FormData(formModalDelete);
    //     // Ajout XHR pour requete vrai delete
    //     console.log(data);
    //     selectedRow.remove();
    // })


  });