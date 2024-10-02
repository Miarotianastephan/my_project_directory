document.addEventListener('DOMContentLoaded', () => {
    const SELECTORS = {
        messageModal: '#messageModal',
        form: '#formulaire',
        cancelButton: '#annuler',
        modifyButton: '#modifier',
        commentField: '#commentaire',
        modalCloseButton: '#messageModal .btn-close',
        modalSecondaryButton: '#messageModal .btn-secondary'
    };

    class FormHandler {
        constructor() {
            this.form = document.querySelector(SELECTORS.form);
            this.cancelUrl = this.form.dataset.cancel;
            this.messageModal = new bootstrap.Modal(document.querySelector(SELECTORS.messageModal));
            this.setupEventListeners();
        }

        setupEventListeners() {
            document.querySelector(SELECTORS.cancelButton).addEventListener('click', this.handleCancel.bind(this));
            document.querySelector(SELECTORS.modifyButton).addEventListener('click', this.handleModify.bind(this));
            document.querySelector(SELECTORS.modalCloseButton).addEventListener('click', this.closeModalAndRedirect.bind(this));
            document.querySelector(SELECTORS.modalSecondaryButton).addEventListener('click', this.closeModalAndRedirect.bind(this));
        }

        handleCancel(e) {
            e.preventDefault();
            window.location.href = this.cancelUrl;
        }

        handleModify(e) {
            e.preventDefault();
            const commentaire = document.querySelector(SELECTORS.commentField).value;

            if (commentaire.trim() === '') {
                this.showMessage('Veuillez ajouter un commentaire.');
                return;
            }

            this.submitForm(commentaire);
        }

        async submitForm(commentaire) {
            try {
                const response = await fetch(this.form.action, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ commentaire })
                });

                if (!response.ok) {
                    throw new Error('Erreur réseau : ' + response.status);
                }

                const data = await response.json();

                if (!data.success) {
                    this.showMessage(data.message);
                } else {
                    window.location.href = data.path;
                }
            } catch (error) {
                console.error('Erreur:', error);
            }
        }

        showMessage(message) {
            const modalBody = document.querySelector(`${SELECTORS.messageModal} .modal-body`);
            modalBody.textContent = message;
            this.messageModal.show();
        }

        closeModalAndRedirect() {
            this.messageModal.hide();
        }
    }

    new FormHandler();
});