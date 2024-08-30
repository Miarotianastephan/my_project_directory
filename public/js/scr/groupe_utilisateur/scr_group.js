
jQuery(document).ready(function () {
    var selectedRow = null;
    var selectedCol = null;
    // var bodyList = document.querySelector('#bodyList');
    var bodyList = $('#bodyList');
    console.log(bodyList)
    var formModalUpdate = document.querySelector('#formModalUpdate');
    var formModalDelete = document.querySelector('#formModalDelete');

    // Pour avoir l'element mere => <tr>
    function getTrElement(theTargetElement){
        return theTargetElement.parentElement.parentElement;
    }
    function setSelectedRow(tr){
        const idOfTrElement = tr.id;
        selectedRow = document.getElementById(idOfTrElement);
        selectedCol = getSelectedRowChildrenWithIds();
        console.log(selectedCol);
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
    function initSelect(){
        var myOptions = [
            { label: 'Options 1', value: '2'},
            { label: 'Options 2', value: '1'},
        ];
        VirtualSelect.init({ 
            ele: '#multipleSelect',
            options: myOptions,
            multiple: false,
            allOptionsSelectedText: 'Tout',
            placeholder: 'Select Dynamic'
        });
    }

    // ---xxx--
    bodyList.on('click', (e) =>{
        target = e.target;
        const tr = getTrElement(target);
        console.log(target)
        if(tr.localName == "tr"){
            setSelectedRow(tr);
            if( target.classList.contains('update') ){
                var colNames = getAllColNames();
                var sectionUpdate = document.querySelector('#sectionUpdate');
                sectionUpdate.innerHTML = ''
                sectionUpdate.innerHTML = 
                `
                <input name="id_prod" value="${selectedRow.id}" type="hidden" readonly />
                <div class="row gy-4 mb-3">
                    <div class="col-md-6" id="modal-id">
                        <label for="id-field" class="form-label">${colNames[0]} </label>
                        <input type="text" id="id-field" class="form-control form-control-sm" name="${selectedCol[0].id}" value="${selectedCol[0].label}" />
                    </div>
                    <div class="col-md-6" id="modal-id">
                        <label for="id-field" class="form-label">${colNames[1]}</label>
                        <input type="text" id="id-field" class="form-control bg-primary-subtle form-control-sm" name="${selectedCol[1].id}" value="${selectedCol[1].label}" />
                    </div>
                </div> 
                <div class="row gy-4 mb-3">
                    <div class="col-md-12" id="modal-id">
                        <label for="id-field" class="form-label">${colNames[2]} </label>
                        <input type="text" id="id-field" class="form-control form-control-sm" name="${selectedCol[2].id}" value="${selectedCol[2].label}" />
                    </div>
                </div>
                `
                console.log("update selected row")
                // initSelect(); // Pour initialiser le select
            }
            if( target.classList.contains('delete') ){
                console.log("delete selected row");
                
                var deleteMessage = document.querySelector('#deleteMessage');
                deleteMessage.innerHTML = '';
                deleteMessage.innerHTML = 
                `Voulez-vous supprimer ${selectedCol[0].label}`;

                var sectionDelete = document.querySelector('#sectionDelete');
                sectionDelete.innerHTML = '';
                sectionDelete.innerHTML = 
                `<input name="id_prod" value="${selectedRow.id}" type="hidden" readonly />`

            }
        }
    })

    // Submit ---xxx---
    // UPDATE
    formModalUpdate.addEventListener('submit', (e)=> {
        e.preventDefault();
        var data = new FormData(formModalUpdate);
        // Ajout XHR pour requete vrai update
        selectedCol[0].elem.innerText = data.get(selectedCol[0].id);
        selectedCol[1].elem.innerText = data.get(selectedCol[1].id);
        selectedCol[2].elem.innerText = data.get(selectedCol[2].id);
        console.log(data);
    })
    // DELETE
    formModalDelete.addEventListener('submit', (e)=> {
        e.preventDefault();
        var data = new FormData(formModalDelete);
        // Ajout XHR pour requete vrai delete
        console.log(data);
        selectedRow.remove();
    })

});