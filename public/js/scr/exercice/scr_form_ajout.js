document.addEventListener('DOMContentLoaded', () => {
    const SELECTORS = {
        formulaire: '#formulaire', messageModal: '#messageModal', annuler: '#annuler', valider: '#valider'
    };

    const ELEMENTS = {
        formulaire: document.querySelector(SELECTORS.formulaire),
        messageModal: new bootstrap.Modal(document.querySelector(SELECTORS.messageModal)),
    };

    function setupEventListeners() {
        //document.querySelector(SELECTORS.annuler).addEventListener('click', handleCancel);
        document.querySelector('#annuler').addEventListener('click', handleCancel);
        document.querySelector(SELECTORS.valider).addEventListener('click', handleSubmit);
        document.querySelector(`${SELECTORS.messageModal} .btn-secondary`).addEventListener('click', closeModalAndRedirect);
        document.querySelector(`${SELECTORS.messageModal} .btn-close`).addEventListener('click', closeModalAndRedirect);
    }

    function closeModalAndRedirect() {
        ELEMENTS.messageModal.hide();
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

    function handleSubmit(e) {
        e.preventDefault();
        //sendRequest(ELEMENTS.formulaire.action, handleSubmitResponse);
        fetch(ELEMENTS.formulaire.action, {
            method: 'POST', headers: {
                'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest'
            }, body: JSON.stringify({
                date_debut: document.getElementById('date_debut').value,
                date_fin: document.getElementById('date_fin').value
            })
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Erreur réseau : ' + response.status);
                }
                return response.json();
            })
            .then(handleSubmitResponse)
            .catch(handleError);
    }

    function handleSubmitResponse(data) {
        if (!data.success) {
            showMessage(data.message);
        } else if (data.url) {
            window.location.href = data.url;
        } else {
            alert("URL introuvable");
        }
    }

    function showMessage(message) {
        const modalBody = document.querySelector(`${SELECTORS.messageModal} .modal-body`);
        modalBody.textContent = message;
        ELEMENTS.messageModal.show();
    }

    function handleError(error) {
        console.error('Erreur:', error);
        showMessage('Une erreur est survenue : ' + error.message);
    }

    setupEventListeners();
});