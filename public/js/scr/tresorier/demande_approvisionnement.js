// public/js/approvisionnement.js
document.addEventListener('DOMContentLoaded', function () {
    const formulaire = document.getElementById("formulaire");
    const messageModal = new bootstrap.Modal(document.getElementById('messageModal'));

    document.getElementById('valider').addEventListener('click', handleSubmit);
    document.getElementById('annuler').addEventListener('click', handleCancel);

    function handleSubmit(e) {
        e.preventDefault();
        const formData = new FormData(formulaire);

        // Log form data for debugging
        for (let [key, value] of formData.entries()) {
            console.log(key, value);
        }

        fetch(formulaire.action, {
            method: 'POST',
            body: formData
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

    function handleResponse(data) {
        if(data.success){
            window.location.href = data.path;
        }else {
            const modalBody = document.querySelector('#messageModal .modal-body');
            modalBody.textContent = data.message;
            messageModal.show();
        }
    }

    function handleError(error) {
        console.error('Erreur:', error);
        //alert('Une erreur est survenue : ' + error.message);
        const modalBody = document.querySelector('#messageModal .modal-body');
        modalBody.textContent = error.message;
        messageModal.show();
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