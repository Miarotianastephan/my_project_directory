// public/js/remise_fond.js
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('formulaire');
    const validerBtn = document.getElementById('valider');
    const annulerBtn = document.getElementById('annuler');

    validerBtn.addEventListener('click', handleSubmit);
    annulerBtn.addEventListener('click', handleCancel);

    function handleSubmit(e) {
        e.preventDefault();
        sendRequest(form.action, { message: "tongasoa" }, handleResponse);
    }

    function handleCancel(e) {
        e.preventDefault();
        const cancelUrl = form.dataset.cancel;
        if (cancelUrl) {
            window.location.href = cancelUrl; // Redirection vers l'URL d'annulation
        } else {
            console.error("Aucune URL d'annulation définie.");
        }
        // Uncomment the next line when you want to enable redirection
        // window.location.href = '/tresorier';
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
            .catch(error => console.error('Erreur:', error));
    }

    function handleResponse(data) {
        if (data.path) {
            window.location.href = data.path;
        } else {
            console.error('Pas de chemin de redirection spécifié dans la réponse');
        }
    }
});