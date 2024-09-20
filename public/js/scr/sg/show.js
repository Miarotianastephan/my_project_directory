function loadTextFile(filePath) {
    fetch(filePath)
        .then(response => response.text())
        .then(data => {
            var textModal = new bootstrap.Modal(document.getElementById('textModal'));
            document.getElementById('text-preview').textContent = data;
            textModal.show();
        })
        .catch(error => console.error('Erreur lors du chargement du fichier texte :', error));
}

function loadExcelPreview(filePath) {
    fetch(filePath)
        .then(response => response.arrayBuffer())
        .then(data => {
            var workbook = XLSX.read(data, {type: 'array'});
            var sheetName = workbook.SheetNames[0];
            var sheet = workbook.Sheets[sheetName];
            var html = XLSX.utils.sheet_to_html(sheet);
            document.getElementById('excel-preview').innerHTML = html;
        })
        .catch(error => console.error('Erreur lors du chargement du fichier Excel :', error));
}

function loadPdfFile(filePath) {
    document.getElementById('pdf-preview').src = filePath;
    var pdfModal = new bootstrap.Modal(document.getElementById('pdfModal'));
    pdfModal.show();
}

document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('formulaire');
    const demandeId = '{{ demande_type.Id }}'; // Assurez-vous de passer cette valeur depuis Twig

    document.getElementById('valider').addEventListener('click', handleValider);
    document.getElementById('refuser').addEventListener('click', handleRefuser);
    document.getElementById('modifier').addEventListener('click', handleModifier);

    /*function handleAction(e, action) {
        e.preventDefault();
        window.location.href = `/sg/demande/${action}/${demandeId}`;
    }*/


    function handleValider(e) {
        e.preventDefault();
        //window.location.href = '/sg';
        const cancelUrl = form.dataset.valider;
        if (cancelUrl) {
            window.location.href = cancelUrl; // Redirection vers l'URL d'annulation
        } else {
            console.error("Aucune URL d'annulation définie.");
        }
    }

    function handleRefuser(e) {
        e.preventDefault();
        //window.location.href = '/sg';
        const cancelUrl = form.dataset.refuse;
        if (cancelUrl) {
            window.location.href = cancelUrl; // Redirection vers l'URL d'annulation
        } else {
            console.error("Aucune URL d'annulation définie.");
        }
    }

    function handleModifier(e) {
        e.preventDefault();
        //window.location.href = '/sg';
        const cancelUrl = form.dataset.update;
        if (cancelUrl) {
            window.location.href = cancelUrl; // Redirection vers l'URL d'annulation
        } else {
            console.error("Aucune URL d'annulation définie.");
        }
    }
});