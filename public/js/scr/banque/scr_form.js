document.addEventListener('DOMContentLoaded', function () {
    const SELECTORS = {
        formulaire: '#formulaire',
        messageModal: '#messageModal',
        annuler: '#annuler',
    };

    const ELEMENTS = {
        formulaire: document.querySelector(SELECTORS.formulaire),
        messageModal: new bootstrap.Modal(document.querySelector(SELECTORS.messageModal)),
    };

    function setupEventListeners() {
        document.querySelector(SELECTORS.annuler).addEventListener('click', handleCancel);
    }

    function handleCancel(e) {
        e.preventDefault();
        const cancelUrl = ELEMENTS.formulaire.dataset.cancel;
        if (cancelUrl) {
            window.location.href = cancelUrl; // Redirection vers l'URL d'annulation
        } else {
            alert("Aucune URL d'annulation définie.");
        }
    }
}