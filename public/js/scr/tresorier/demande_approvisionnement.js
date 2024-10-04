// public/js/approvisionnement.js
document.addEventListener('DOMContentLoaded', function () {
    const formulaire = document.getElementById("formulaire");
    const messageModal = new bootstrap.Modal(document.getElementById('messageModal'));

    // document.getElementById('valider').addEventListener('click', handleSubmit);
    document.getElementById('annuler').addEventListener('click', handleCancel);

    function handleSubmit(e) {
        e.preventDefault();
        const formData = getFormData();

        fetch(formulaire.action, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(formData)
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Erreur réseau : ' + response.status);
                }
                return response.json();
            })
            .then(handleResponse)
            .catch(handleError);
    }

    function getFormData() {
        return {
            date_dm: document.getElementById('date_dm').value,
            caisse: document.getElementById('caisse').value,
            entite: document.getElementById('entite').value,
            montant: document.getElementById('montant').value,
            banque: document.getElementById('banque').value,
            chequier: document.getElementById('chequier').value
        };
    }

    function handleResponse(data) {
        const modalBody = document.querySelector('#messageModal .modal-body');
        if (!data.success) {
            modalBody.textContent = data.message;
            messageModal.show();
        } else {
            if (data.url) {
                alert(data.message);
                // window.location.href = data.url;
            } else {
                alert("Impossible de trouver l'URL");
            }
        }
    }

    function handleError(error) {
        console.error('Erreur:', error);
        alert('Une erreur est survenue : ' + error.message);
    }

    function handleCancel(e) {
        e.preventDefault();
        const cancelUrl = formulaire.dataset.cancel;
        if (cancelUrl) {
            window.location.href = cancelUrl; // Redirection vers l'URL d'annulation
        } else {
            console.error("Aucune URL d'annulation définie.");
        }
    }
});