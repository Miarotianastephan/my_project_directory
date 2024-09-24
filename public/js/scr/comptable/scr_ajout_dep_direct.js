// depense_directe.js
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('formulaire');
    const transactionSelect = document.getElementById('transaction');
    const planCompteSelect = document.getElementById('plan_compte');
    const validerButton = document.getElementById('valider');

    if (!form || !transactionSelect || !planCompteSelect || !validerButton) {
        console.error('One or more required elements not found');
        return;
    }

    let transactionCompteMap = {};

    function updatePlanCompteOptions() {
        const selectedTransactionId = transactionSelect.value;
        planCompteSelect.innerHTML = '';

        if (transactionCompteMap[selectedTransactionId]) {
            populatePlanCompteOptions(transactionCompteMap[selectedTransactionId]);
        } else {
            fetch(`/get-transaction-details?transactionId=${selectedTransactionId}`)
                .then(response => response.json())
                .then(data => {
                    transactionCompteMap[selectedTransactionId] = data;
                    populatePlanCompteOptions(data);
                })
                .catch(error => console.error('Erreur:', error));
        }
    }

    function populatePlanCompteOptions(comptes) {
        comptes.forEach(compte => {
            const option = document.createElement('option');
            option.value = compte.id;
            option.textContent = `${compte.numero} - ${compte.libelle}`;
            planCompteSelect.appendChild(option);
        });
    }

    function sendData(e) {
        e.preventDefault();
        fetch(form.action, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: new FormData(form)
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Erreur réseau : ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                console.log(data.message);
                // Handle successful response here
            })
            .catch(error => console.error('Erreur:', error));
    }

    transactionSelect.addEventListener('change', updatePlanCompteOptions);
    validerButton.addEventListener('click', sendData);

    // Initial population of plan_compte options
    updatePlanCompteOptions();
});