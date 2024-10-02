document.addEventListener('DOMContentLoaded', () => {
    let exercice_selected = "";

    const SELECTORS = {
        messageModal: '#messageModal',
        ouvrir: '.ouvrir-btn',
        cloturer: '.cloturer-btn',
        ouvertureModal: '#ouvertureModal',
        clotureModal: '#clotureModal'
    };

    const ELEMENTS = {
        ouvertureModal: new bootstrap.Modal(document.querySelector(SELECTORS.ouvertureModal)),
        clotureModal: new bootstrap.Modal(document.querySelector(SELECTORS.clotureModal)),
        messageModal: new bootstrap.Modal(document.querySelector(SELECTORS.messageModal)),
    };

    function setupEventListeners() {
        //document.querySelector(SELECTORS.ouvrir).addEventListener('click', showModalOuvrir);
        //document.querySelector(SELECTORS.cloturer).addEventListener('click', showModalCloturer);
        document.querySelectorAll('.ouvrir-btn').forEach(function (button) {
            button.addEventListener('click', showModalOuvrir);
        });
        document.querySelectorAll('.cloturer-btn').forEach(function (button) {
            button.addEventListener('click', showModalCloturer);
        });

        document.querySelector("#ouvertureModal .btn-close").addEventListener('click', closeModal);
        document.querySelector("#ouvertureModal #non").addEventListener('click', closeModal);
        //document.querySelector("#ouvertureModal #oui").addEventListener('click', ouvrirExercice);
        document.getElementById("oui-ouverture").addEventListener('click', ouvrirExercice);
        document.getElementById("oui-cloturer").addEventListener('click', cloturerExercice);
    }

    function closeModal() {
        ELEMENTS.ouvertureModal.hide();
    }

    function showMessage(message) {
        const modalBody = document.querySelector(`${SELECTORS.messageModal} .modal-body`);
        modalBody.textContent = message;
        ELEMENTS.messageModal.show();
    }

    function showModalOuvrir(event) {
        const button = event.currentTarget;
        const url = button.dataset.url;
        getDataController(url, ELEMENTS.ouvertureModal, updateOuvertureModalFields);
    }

    function showModalCloturer(event) {
        const button = event.currentTarget;
        const url = button.dataset.url;
        exercice_selected = button.getAttribute('data-id');

        getDataController(url, ELEMENTS.clotureModal, updateClotureModalFields);
    }

    function getDataController(url, modal, updateFunction) {
        fetch(url, {
            method: 'GET', headers: {
                'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest'
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
        exercice_selected = data.id;
        document.querySelector('#ouvertureModal #id-input').value = data.id;
        document.querySelector('#ouvertureModal #date-debut-input').value = data.ExerciceDateDebut;
    }

    function updateClotureModalFields(data) {
        exercice_selected = data.id;
        document.querySelector('#clotureModal #id-input').value = data.id;
        document.querySelector('#clotureModal #date-debut-input').value = data.ExerciceDateDebut;
    }

    function ouvrirExercice() {
        const url = `/exercice/ouverture/${exercice_selected}`;

        fetch(url, {
            method: 'POST', //body: formData,
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
                    showMessage(data.message);
                    closeModal();
                    // Afficher un message d'erreur
                    //console.error('Erreur lors de l\'ouverture de l\'exercice:', data.message);
                }
            })
            .catch(error => {
                console.error('Erreur lors de l\'ouverture de l\'exercice:', error);
            });
    }

    function cloturerExercice() {
        const url = `/exercice/cloturer/${exercice_selected}`;
        fetch(url, {
            method: 'POST', body: JSON.stringify({
                'date_fin': document.querySelector('#date-fin-input').value
            }), headers: {
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
                    showMessage(data.message);
                    closeModal();
                    // Afficher un message d'erreur
                    //console.error('Erreur lors de la cloture de l\'exercice:', data.message);
                }
            })
            .catch(error => {
                console.error('Erreur lors de la cloture de l\'exercice:', error);
            });
    }

    setupEventListeners();
});