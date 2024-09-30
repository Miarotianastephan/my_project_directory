document.addEventListener('DOMContentLoaded', () => {
    const SELECTORS = {
        ouvrir: '#ouvrir', ouvertureModal: '#ouvertureModal', cloturer: '#cloturer', clotureModal: '#clotureModal',
    };

    const ELEMENTS = {
        ouvertureModal: new bootstrap.Modal(document.querySelector(SELECTORS.ouvertureModal)),
        clotureModal: new bootstrap.Modal(document.querySelector(SELECTORS.clotureModal)),
    };

    function setupEventListeners() {
        // Ajouter un écouteur d'événements pour le bouton "Ouvrir"
        document.getElementById("ouvrir-btn").addEventListener('click', showModalOuvrir);
        document.getElementById("cloturer-btn").addEventListener('click', showModalCloturer);

        document.querySelector(".btn-close").addEventListener('click', closeModalAndRedirect);

        /*document.querySelectorAll(SELECTORS.ouvrir).forEach(button => {
            console.log(button);
            button.addEventListener('click', showModalOuvrir);
        });
        document.querySelectorAll(SELECTORS.cloturer).forEach(button => {
            console.log(button);

            button.addEventListener('click', showModalCloturer);
        });*/

        //console.log(`${SELECTORS.ouvertureModal} .btn-secondary`);
        //document.querySelector(`${SELECTORS.ouvertureModal} .btn-secondary`).addEventListener('click', closeModalAndRedirect);
        //document.querySelector(`${SELECTORS.ouvertureModal} .btn-close`).addEventListener('click', closeModalAndRedirect);
    }

    function closeModalAndRedirect() {
        ELEMENTS.ouvertureModal.hide();
    }

    function showModalOuvrir(event) {
        const button = event.currentTarget;
        const url = button.dataset.url; // Récupère l'URL à partir de l'attribut data-url
        try {
            const data = getDataController(url);
            //console.log(data);

            //updateModalFields(data);
            ELEMENTS.ouvertureModal.show();
        } catch (error) {
            console.error('Erreur lors de l\'ouverture du modal :', error);
        }
    }

    function showModalCloturer(event) {
        const button = event.currentTarget;
        const url = button.dataset.url; // Récupère l'URL à partir de l'attribut data-url
        try {
            const data = getDataController(url);
            //console.log(data);
            //updateModalFields(data);
            ELEMENTS.clotureModal.show();
        } catch (error) {
            console.error('Erreur lors de l\'ouverture du modal de clôture :', error);
        }

    }

    function getDataController(url) {
        fetch(url, {
            method: 'GET', headers: {
                'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Erreur réseau : ' + response.status);
                }
                console.log(response);
                //updateModalFields(response)
                //return response;
                //return response.json();
            })
            .then(data => {
                //console.log(data);
                return data
            })
            .catch(error => {
                return error;
                //console.error('Erreur lors de la récupération des données :', error);
            });
    }


    function updateModalFields(donne) {
        document.getElementById('id-input').value = donne.id;
        document.getElementById('date-debut-input').value = donne.ExerciceDateDebut;
        document.getElementById('date-debut-fin').value = donne.ExerciceDateFin;
    }


    function getDataOuvrir() {
        fetch("url", {
            method: 'get', headers: {
                'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest'
            }, body: JSON.stringify(data)
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Erreur réseau : ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                console.log(data)
            })
            .catch();
    }

    // Appeler setupEventListeners pour initialiser les écouteurs d'événements
    setupEventListeners();
});