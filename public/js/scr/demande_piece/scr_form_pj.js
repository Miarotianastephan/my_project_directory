document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('formulaire');
    const fileInput = document.getElementById('customFile');
    const previewsContainer = document.getElementById('imagePreviews');
    const validationButton = document.getElementById('valider');
    const cancelButton = document.getElementById('annuler');

    validationButton.addEventListener('click', handleSubmit);
    cancelButton.addEventListener('click', handleCancel);
    fileInput.addEventListener('change', handleFileSelect);

    function handleSubmit(e) {
        e.preventDefault();
        if (!validateForm()) return;

        const formData = new FormData(form);
        sendFormData(formData);
    }

    function validateForm() {
        const files = fileInput.files;
        if (files.length === 0) {
            showError('Veuillez sélectionner au moins un fichier.');
            return false;
        }
        return true;
    }

    function sendFormData(formData) {
        fetch(form.action, {
            method: 'POST', headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }, body: formData
        })
            .then(response => {
                if (!response.ok) throw new Error('Erreur réseau : ' + response.status);
                return response.json();
            })
            .then(handleResponse)
            .catch(error => showError('Erreur : ' + error.message));
    }

    function handleResponse(data) {
        if (data.success) {
            window.location.href = data.path;
        } else {
            showError(data.message);
        }
    }

    function handleCancel(e) {
        e.preventDefault();
        window.location.href = form.dataset.cancel;
    }

    function handleFileSelect() {
        previewsContainer.innerHTML = '';
        Array.from(fileInput.files).forEach(createFilePreview);
    }

    function createFilePreview(file) {
        const preview = document.createElement('div');
        preview.className = 'col-md-4 mb-3';
        preview.innerHTML = getPreviewContent(file);
        previewsContainer.appendChild(preview);
    }

    function getPreviewContent(file) {
        const iconClass = getFileIconClass(file.type);
        return `
            <div class="card">
                <div class="card-body text-center">
                    <i class="${iconClass} fa-3x mb-2"></i>
                    <p class="mb-0">${file.name}</p>
                    <small>${(file.size / 1024).toFixed(2)} KB</small>
                </div>
            </div>
        `;
    }

    function getFileIconClass(fileType) {
        if (fileType.match('image.*')) return 'fas fa-file-image text-primary';
        if (fileType === 'application/pdf') return 'fas fa-file-pdf text-danger';
        if (fileType.match('excel')) return 'fas fa-file-excel text-success';
        return 'fas fa-file text-secondary';
    }

    function showError(message) {
        alert(message);  // Pour une meilleure UX, remplacez ceci par une notification plus élégante
    }
});