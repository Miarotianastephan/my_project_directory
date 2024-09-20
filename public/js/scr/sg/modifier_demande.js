document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('formulaire');
    const cancelUrl = form.dataset.cancel;

    document.getElementById('annuler').addEventListener('click', function (e) {
        e.preventDefault();
        window.location.href = cancelUrl; // Redirige vers la page en cas d'annulation
    });

    document.getElementById('modifier').addEventListener('click', function (e) {
        e.preventDefault();

        // Récupérer la valeur du champ commentaire
        const commentaire = document.getElementById('commentaire').value;

        // Vérification simple si le champ commentaire est vide
        if (commentaire.trim() === '') {
            alert('Veuillez ajouter un commentaire.');
            return;
        }

        fetch(form.action, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                "commentaire": commentaire
            })
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Erreur réseau : ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                if (!data.success) {
                    alert(data.message);
                } else {
                    window.location.href = data.path; // Redirection après modification réussie
                }
            })
            .catch(error => console.error('Erreur:', error));
    });
});
