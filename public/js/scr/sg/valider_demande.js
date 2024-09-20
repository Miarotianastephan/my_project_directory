document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('formulaire');
    const validerBtn = document.getElementById('valider');
    const annulerBtn = document.getElementById('annuler');
    const modifierBtn = document.getElementById('modifier');

    const SELECTORS = {messageModal: '#messageModal'}
    const ELEMENTS = {messageModal: new bootstrap.Modal(document.querySelector(SELECTORS.messageModal)),}

    validerBtn.addEventListener('click', handleValidation);
    annulerBtn.addEventListener('click', handleAnnulation);
    modifierBtn.addEventListener('click', handleModification);
    function closeModalAndRedirect() {
        ELEMENTS.messageModal.hide();
    }
    function showMessage(message) {
        const modalBody = document.querySelector(`${SELECTORS.messageModal} .modal-body`);
        modalBody.textContent = message;
        ELEMENTS.messageModal.show();
    }

    function handleValidation(e) {
        e.preventDefault();
        fetch(form.action, {
            method: 'POST', headers: {
                'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest'
            }, body: JSON.stringify({message: "tongasoa"})
        })
            .then(response => {
                if (!response.ok) throw new Error('Erreur réseau : ' + response.status);
                return response.json();
            })
            .then(data => {
                if (!data.success) {
                    showMessage(data.message);
                } else {
                    window.location.href = data.path;
                }
            })
            .catch(error => console.error('Erreur:', error));
    }

    function handleAnnulation(e) {
        e.preventDefault();
        //window.location.href = '/sg';
        const cancelUrl = form.dataset.cancel;
        if (cancelUrl) {
            window.location.href = cancelUrl; // Redirection vers l'URL d'annulation
        } else {
            console.error("Aucune URL d'annulation définie.");
        }
    }

    function handleModification(e) {
        e.preventDefault();
        //const demandeId = form.action.split('/').pop();
        //window.location.href = `/sg/demande/modifier/${demandeId}`;
        const modifier = form.dataset.update;
        if (modifier) {
            window.location.href = modifier; // Redirection vers l'URL d'annulation
        } else {
            console.error("Aucune URL d'annulation définie.");
        }
    }
});