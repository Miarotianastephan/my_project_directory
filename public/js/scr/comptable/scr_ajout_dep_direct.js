document.addEventListener('DOMContentLoaded', () => {
    const transactionSelect = document.getElementById('transaction');
    const planCompteSelect = document.getElementById('plan_compte');
    const loadingOption = document.createElement('option');
    loadingOption.textContent = 'Chargement...';

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