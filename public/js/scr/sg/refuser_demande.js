document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('formulaire');
    const commentaire = document.getElementById('commentaire');
    document.getElementById('annuler').addEventListener('click', handleAnnuler);

    const SELECTORS = {messageModal: '#messageModal'}
    const ELEMENTS = {messageModal: new bootstrap.Modal(document.querySelector(SELECTORS.messageModal)),}

    function showMessage(message) {
        const modalBody = document.querySelector(`${SELECTORS.messageModal} .modal-body`);
        modalBody.textContent = message;
        ELEMENTS.messageModal.show();
    }

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        submitForm();
    });


    function handleAnnuler(e) {
        e.preventDefault();
        const cancelUrl = form.dataset.cancel;
        if (cancelUrl) {
            window.location.href = cancelUrl; // Redirection vers l'URL d'annulation
        } else {
            console.error("Aucune URL d'annulation définie.");
        }
    }

    function submitForm() {
        fetch(form.action, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                commentaire: commentaire.value
            })
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
            .catch(error => {
                console.error('Erreur:', error);
                showMessage('Une erreur est survenue. Veuillez réessayer.');
            });
    }
});