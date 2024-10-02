// public/js/budget_management.js
document.addEventListener('DOMContentLoaded', function () {
    const SELECTORS = {
        formulaire: '#formulaire',
        messageModal: '#messageModal',
        annuler: '#annuler',
        valider: '#valider'
    };

    const ELEMENTS = {
        formulaire: document.querySelector(SELECTORS.formulaire),
        messageModal: new bootstrap.Modal(document.querySelector(SELECTORS.messageModal)),
    };

    function setupEventListeners() {
        document.querySelector(SELECTORS.annuler).addEventListener('click', handleCancel);
        document.querySelector(SELECTORS.valider).addEventListener('click', handleSubmit);
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
        alert("formulaire valider");
        //const formData = getFormData();
        //sendRequest(ELEMENTS.formulaire.action, formData, handleSubmitResponse);
    }
    /*
    function sendRequest(url,data) {
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
            alert(JSON.stringify(data.message));
        } else {
            updateModalFields(data);
            ELEMENTS.updateAmountModal.show();
        }
    }

    function handleUpdateResponse(data) {
        if (!data.success) {
            showMessage(data.message);
        } else if (data.url) {
            alert(JSON.stringify(data.message));
        } else {
            alert("impossible de trouver url");
        }
    }

    function showMessage(message) {
        const modalBody = document.querySelector(`${SELECTORS.messageModal} .modal-body`);
        modalBody.textContent = message;
        ELEMENTS.messageModal.show();
    }*/



    function handleError(error) {
        console.error('Erreur:', error);
        alert('Une erreur est survenue : ' + error.message);
    }

    setupEventListeners();
});