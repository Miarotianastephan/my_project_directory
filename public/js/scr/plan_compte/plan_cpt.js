jQuery(document).ready(function() {

    var form = document.getElementById('add_cpt_form');
    
    // Ajouter un compte
    document.getElementById('add_cpt_form').addEventListener('submit', (e)=>{
        e.preventDefault();
        // revoir
        ajouterCompte();
    });

    // Update d'un compte selectionner
    document.getElementById('save-modif').addEventListener('click', (e)=>{
        sauvegarderModification();
    })

    // Importer les comptes par fichier XLSX
    document.getElementById('add-plan').addEventListener('click', (e)=>{
        e.preventDefault();
        console.log("test");
        importerFichierExcel();
    });

    // Sauver les modifications des comptes 
    var form_save_compte = document.getElementById('form_save_compte');
    form_save_compte.addEventListener('submit', (e)=>{
        e.preventDefault();
        const url_to_save_compte = form_save_compte.action;
        const data_compte = hierarchies;
        const xhr = new newXhr();
        xhr.onreadystatechange = function(){
            if (this.readyState == 4 && this.status == 200){
                const rep = this.response;
                if (rep.status == false){
                    appendAlert(rep.message, 'danger');
                }
                else if (rep.status == true){
                    appendAlert(rep.message, 'success');
                    // window.location.href = rep.path; // to admin
                }
            }
            else if(this.readyState == 4){
                appendAlert('Erreur de traitement de requete','danger');
            }
        }
        xhr.open('POST', url_to_save_compte, true);
        xhr.responseType = "json";
        xhr.send(JSON.stringify(data_compte));
    });

  
let hierarchies = {};
let comptes = {};
let compteEnEdition = null;

    function ajouterCompte(numero = null, libelle = null) {
        numero = numero || document.getElementById('compte-numero').value;
        libelle = libelle || document.getElementById('compte-libelle').value;

        if (numero && libelle) {
            comptes[numero] = { numero, libelle, enfants: [] };
            updateHierarchie();
            document.getElementById('compte-numero').value = '';
            document.getElementById('compte-libelle').value = '';
        }
    }

    function updateHierarchie() {
        Object.values(comptes).forEach(compte => compte.enfants = []);

        const hierarchie = {};

        // Vérification que 'numero' est bien une chaîne de caractères avant de trier
        Object.values(comptes)
        .map(compte => {
            // Conversion du numéro en chaîne de caractères
            compte.numero = String(compte.numero);
            return compte;
        })
        .sort((a, b) => a.numero.localeCompare(b.numero))
        .forEach(compte => {
            const parent = trouverParent(compte.numero);
            if (parent) {
                comptes[parent].enfants.push(compte);
            } else {
                hierarchie[compte.numero] = compte;
            }
        });
        const ul = document.getElementById('hierarchie-comptes');
        ul.innerHTML = '';
        Object.values(hierarchie).forEach(compte => {
            ul.appendChild(creerElementCompte(compte));
        });
        hierarchies= hierarchie;
    }

    function trouverParent(numero) {
        for (let i = numero.length - 1; i > 0; i--) {
            const parentPotentiel = numero.substring(0, i);
            if (comptes[parentPotentiel]) {
                return parentPotentiel;
            }
        }
        return null;
    }

    function creerElementCompte(compte) {
        const li = document.createElement('li');

        // Création du bouton pour ouvrir/fermer les enfants
        const span = document.createElement('span');
        span.className = `compte ${compte.enfants.length > 0 ? 'compte-mere' : 'compte-enfant'}`;
        span.textContent = `${compte.numero} - ${compte.libelle}`;
        
        // Ajout d'un bouton dropdown si c'est une mère
        if (compte.enfants.length > 0) {
            const dropdownButton = document.createElement('button');
            dropdownButton.className = 'dropdown-button';
            dropdownButton.textContent = '▶'; // Flèche droite
            dropdownButton.onclick = () => {
                const ulEnfants = li.querySelector('ul');
                const isHidden = ulEnfants.style.display === 'none';
                ulEnfants.style.display = isHidden ? 'block' : 'none';
                dropdownButton.textContent = isHidden ? '▼' : '▶'; // Changer l'icône selon l'état
            };
            li.appendChild(dropdownButton);
        }

        span.onclick = () => ouvrirModalEdition(compte);
        li.appendChild(span);

        const actionsDiv = document.createElement('div');
        actionsDiv.className = 'actions';

        const deleteButton = document.createElement('button');
        deleteButton.className = 'delete-button';
        deleteButton.textContent = 'Supprimer';
        deleteButton.onclick = (e) => {
            e.stopPropagation();
            supprimerCompte(compte.numero);
        };
        actionsDiv.appendChild(deleteButton);

        li.appendChild(actionsDiv);

        // Gestion de l'affichage des enfants si le compte a des enfants
        if (compte.enfants && compte.enfants.length > 0) {
            const ulEnfants = document.createElement('ul');
            ulEnfants.style.display = 'none'; // Par défaut, cacher les enfants
            compte.enfants.forEach(enfant => {
                ulEnfants.appendChild(creerElementCompte(enfant));
            });
            li.appendChild(ulEnfants);
        }

        return li;
    }


    function supprimerCompte(numero) {
        delete comptes[numero];
        updateHierarchie();
    }

    function ouvrirModalEdition(compte) {
        compteEnEdition = compte;
        document.getElementById('edit-numero').value = compte.numero;
        document.getElementById('edit-libelle').value = compte.libelle;
        document.getElementById('modal-edit').style.display = 'block';
    }

    function sauvegarderModification() {
        const nouveauNumero = document.getElementById('edit-numero').value;
        const nouveauLibelle = document.getElementById('edit-libelle').value;
        if (nouveauNumero && nouveauLibelle && compteEnEdition) {
            if (nouveauNumero !== compteEnEdition.numero) {
                // Supprimer l'ancien compte et créer un nouveau
                delete comptes[compteEnEdition.numero];
                comptes[nouveauNumero] = { numero: nouveauNumero, libelle: nouveauLibelle, enfants: [] };
            } else {
                // Mettre à jour le libellé seulement
                compteEnEdition.libelle = nouveauLibelle;
            }
            updateHierarchie();
            fermerModalEdition();
        }
    }

    function fermerModalEdition() {
        document.getElementById('modal-edit').style.display = 'none';
        compteEnEdition = null;
    }

    // Fermer le modal lorsqu'on clique sur la croix ou en dehors du modal
    document.querySelector('.close').addEventListener('click', ()=>{
        fermerModalEdition();
    });

    window.onclick = function(event) {
        if (event.target == document.getElementById('modal-edit')) {
            fermerModalEdition();
        }
    }

    // Importer des données depuis un fichier Excel
    function importerFichierExcel() {
        const fileInput = document.getElementById('file-input');
        const file = fileInput.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = function(e) {
            const data = new Uint8Array(e.target.result);
            const workbook = XLSX.read(data, { type: 'array' });
            const sheetName = workbook.SheetNames[0];
            const worksheet = workbook.Sheets[sheetName];
            const rows = XLSX.utils.sheet_to_json(worksheet, { header: 1 });

            // Ajouter chaque compte depuis le fichier Excel
            rows.forEach(row => {
                const [numero, libelle] = row;
                if (numero && libelle) {
                    ajouterCompte(numero, libelle);
                }
            });
            console.log(hierarchies); //pour avoir la hierarchies des comptes 
        };
        reader.readAsArrayBuffer(file);
    }

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

    // Initialisation
    updateHierarchie();
    console.log(comptes);
 
});