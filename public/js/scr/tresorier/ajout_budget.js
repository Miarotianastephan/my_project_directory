document.addEventListener('DOMContentLoaded', function () {
    const SELECTORS = {
        formulaire: '#formulaire',
        modifier: '#modifier',
        messageModal: '#messageModal',
        updateAmountModal: '#updateAmountModal',
        annuler: '#annuler',
        valider: '#valider',
        oui: '#oui'
    };

    const ELEMENTS = {
        formulaire: document.querySelector(SELECTORS.formulaire),
        modifier: document.querySelector(SELECTORS.modifier),
        messageModal: new bootstrap.Modal(document.querySelector(SELECTORS.messageModal)),
        updateAmountModal: new bootstrap.Modal(document.querySelector(SELECTORS.updateAmountModal))
    };

    function setupEventListeners() {
        document.querySelector(SELECTORS.annuler).addEventListener('click', handleCancel);
        document.querySelector(SELECTORS.valider).addEventListener('click', handleSubmit);
        document.querySelector(SELECTORS.oui).addEventListener('click', handleUpdate);
        document.querySelector(`${SELECTORS.messageModal} .btn-secondary`).addEventListener('click', closeModalAndRedirect);
        document.querySelector(`${SELECTORS.messageModal} .btn-close`).addEventListener('click', closeModalAndRedirect);
    }

    function handleCancel(e) {
        e.preventDefault();
        const cancelUrl = ELEMENTS.formulaire.dataset.cancel;
        if (cancelUrl) {
            window.location.href = cancelUrl; // Redirection vers l'URL d'annulation
        } else {
            console.error("Aucune URL d'annulation définie.");
        }
    }

    function closeModalAndRedirect() {
        ELEMENTS.messageModal.hide();
    }

    function handleSubmit(e) {
        e.preventDefault();
        const formData = getFormData();
        sendRequest(ELEMENTS.formulaire.action, formData, handleSubmitResponse);
    }

    function handleUpdate(e) {
        e.preventDefault();
        const formData = getUpdateFormData();
        sendRequest(ELEMENTS.modifier.action, formData, handleUpdateResponse);
    }

    function getFormData() {
        return {
            exercice: document.getElementById('exercice_id').value,
            plan_cpt: document.getElementById('plan_cpt').value,
            montant: document.getElementById('montant').value
        };
    }

    function getUpdateFormData() {
        return {
            detail_budget: document.getElementById('detailbudget-input').value,
            montant: document.getElementById('new-montant-input').value
        };
    }

    function sendRequest(url, data, callback) {
        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(data)
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Erreur réseau : ' + response.status);
                }
                return response.json();
            })
            .then(callback)
            .catch(handleError);
    }

    function handleSubmitResponse(data) {
        if (!data.success) {
            showMessage(data.message);
        } else if (data.url) {
            window.location.href = data.url;
        } else {
            updateModalFields(data);
            ELEMENTS.updateAmountModal.show();
        }
    }

    function handleUpdateResponse(data) {
        if (!data.success) {
            showMessage(data.message);
        } else if (data.url) {
            window.location.href = data.url;
        } else {
            alert("impossible de trouver url");
        }
    }

    function showMessage(message) {
        const modalBody = document.querySelector(`${SELECTORS.messageModal} .modal-body`);
        modalBody.textContent = message;
        ELEMENTS.messageModal.show();
    }

    function updateModalFields(data) {
        document.getElementById('new-exercice-input').value = data.exercice.id;
        document.getElementById('new-cpt-input').value = data.cpt.id;
        document.getElementById('old-montant-input').value = data.oldmontant;
        document.getElementById('new-montant-input').value = data.newmontant;
        document.getElementById('detailbudget-input').value = data.detailbudget;

        console.log(data.exercice.DateDebut)
        //document.getElementById('new-exercice').textContent = formattedDate.toLocaleDateString();
        document.getElementById('new-cpt').textContent = data.cpt.CptLibelle;
        document.getElementById('old-montant').textContent = data.oldmontant + ' Ar';
        document.getElementById('new-montant').textContent = data.newmontant + ' Ar';
    }

    function handleError(error) {
        //console.error('Erreur:', error);
        //alert('Une erreur est survenue : ' + error.message);
        showMessage(error.message)
    }

    setupEventListeners();
});