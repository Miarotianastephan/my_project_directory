document.addEventListener('DOMContentLoaded', () => {
    const transactionSelect = document.getElementById('transaction');
    const formulaire = document.getElementById('formulaire');
    const planCompteSelect = document.getElementById('plan_compte');
    const loadingOption = document.createElement('option');
    loadingOption.textContent = 'Chargement...';
    document.querySelector(`#valider`).addEventListener('click', handleSubmit);
    document.querySelector(`#messageModal .btn-close`).addEventListener('click', closeModalAndRedirect);

    function closeModalAndRedirect() {
        new bootstrap.Modal(document.querySelector("#messageModal")).hide();
    }

    function handleSubmit(e) {
        e.preventDefault();
        sendRequest(formulaire.action);
    }

    function sendRequest(url) {
        fetch(url, {
            method: 'POST', headers: {
                'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest'
            }, body: JSON.stringify({
                entite: document.getElementById("entite").value,
                transaction: document.getElementById("transaction").value,
                plan_compte: document.getElementById("plan_compte").value,
                montant: document.getElementById("montant").value,
            })
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Erreur réseau : ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                showMessage(data.message);
            })
            .catch(error => {
                console.error(error);
                showMessage(error.message);
            });
    }

    function showMessage(message) {
        const modalBody = document.querySelector(`#messageModal .modal-body`);
        modalBody.textContent = message;
        messageModal: new bootstrap.Modal(document.querySelector("#messageModal")).show();
    }

    function getOptions() {
        const selectedTransactionId = transactionSelect.value;

        if (!selectedTransactionId) {
            planCompteSelect.innerHTML = '<option value="">Sélectionnez un compte</option>';
            return;
        }

        planCompteSelect.innerHTML = '';
        planCompteSelect.appendChild(loadingOption);
        fetch(`/comptable/get-transaction-details?transactionId=${selectedTransactionId}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Erreur réseau');
                }
                return response.json();
            })
            .then(data => {
                //planCompteSelect.innerHTML = '<option value="">Sélectionnez un compte</option>';
                planCompteSelect.innerHTML = '';
                data.forEach(compte => {
                    const option = document.createElement('option');
                    option.value = compte.id;
                    option.textContent = `${compte.numero} - ${compte.libelle}`;
                    planCompteSelect.appendChild(option);
                });
            })
            .catch(error => {
                console.error('Erreur:', error);
                planCompteSelect.innerHTML = '<option value="">Erreur de chargement</option>';
            });
    }

    transactionSelect.addEventListener('change', getOptions);
    getOptions(); // Initial call to populate based on default selection
});