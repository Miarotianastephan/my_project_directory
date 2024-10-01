document.addEventListener('DOMContentLoaded', () => {
    const SELECTORS = {
        ouvrir: '#ouvrir-btn',
        cloturer: '#cloturer-btn',
        ouvertureModal: '#ouvertureModal',
        clotureModal: '#clotureModal'
    };

    const ELEMENTS = {
        ouvertureModal: new bootstrap.Modal(document.querySelector(SELECTORS.ouvertureModal)),
        clotureModal: new bootstrap.Modal(document.querySelector(SELECTORS.clotureModal)),
    };

    function setupEventListeners() {
        document.querySelector(SELECTORS.ouvrir).addEventListener('click', showModalOuvrir);
        document.querySelector(SELECTORS.cloturer).addEventListener('click', showModalCloturer);
        document.querySelector("#ouvertureModal .btn-close").addEventListener('click', closeModal);
        document.querySelector("#ouvertureModal #non").addEventListener('click', closeModal);
        document.querySelector("#ouvertureModal #oui").addEventListener('click', ouvrirExercice);
    }

    function closeModal() {
        ELEMENTS.ouvertureModal.hide();
    }

    function showModalOuvrir(event) {
        const button = event.currentTarget;
        const url = button.dataset.url;
        getDataController(url, ELEMENTS.ouvertureModal, updateOuvertureModalFields);
    }

    function showModalCloturer(event) {
        const button = event.currentTarget;
        const url = button.dataset.url;
        getDataController(url, ELEMENTS.clotureModal, updateClotureModalFields);
        // Implémentation similaire à showModalOuvrir si nécessaire
    }

    function getDataController(url, modal, updateFunction) {
        fetch(url, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Erreur réseau : ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                updateFunction(data);
                modal.show();
            })
            .catch(error => {
                console.error('Erreur lors de la récupération des données :', error);
            });
    }

    function updateOuvertureModalFields(data) {
        document.querySelector('#ouvertureModal #id-input').value = data.id;
        document.querySelector('#ouvertureModal #date-debut-input').value = data.ExerciceDateDebut;
    }

    function updateClotureModalFields(data) {
        console.log(data);
        document.querySelector('#clotureModal #id-input').value = data.id;
        document.querySelector('#clotureModal #date-debut-input').value = data.ExerciceDateDebut;
    }

    function ouvrirExercice() {
        const form = document.querySelector('#ouvertureModal form');
        const formData = new FormData(form);

        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    closeModal();
                    // Rafraîchir la page ou mettre à jour l'interface utilisateur
                    location.reload();
                } else {
                    // Afficher un message d'erreur
                    console.error('Erreur lors de l\'ouverture de l\'exercice:', data.message);
                }
            })
            .catch(error => {
                console.error('Erreur lors de l\'ouverture de l\'exercice:', error);
            });
    }

    setupEventListeners();
});