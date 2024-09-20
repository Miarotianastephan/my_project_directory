document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('formulaire');
    const commentaire = document.getElementById('commentaire');
    document.getElementById('annuler').addEventListener('click', handleAnnuler);

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        if (!commentaire.value.trim()) {
            alert('Veuillez saisir un motif de refus.');
            return;
        }
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
                    alert(data.message);
                } else {
                    window.location.href = data.path;
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                alert('Une erreur est survenue. Veuillez réessayer.');
            });
    }
});