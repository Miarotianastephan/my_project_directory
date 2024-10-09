function loadTextFile(filePath) {
    fetch(filePath)
        .then(response => response.text())
        .then(data => {
            // Affiche le contenu du fichier texte dans le modal
            var textModal = new bootstrap.Modal(document.getElementById('textModal'));

            document.getElementById('text-preview').textContent = data;
            textModal.show();
        })
        .catch(error => console.error('Erreur lors du chargement du fichier texte :', error));
}


// Fonction pour charger un aperçu d'un fichier Excel
function loadExcelPreview(filePath) {
    fetch(filePath)
        .then(response => response.arrayBuffer())
        .then(data => {
            var workbook = XLSX.read(data, {type: 'array'});
            var sheetName = workbook.SheetNames[0];
            var sheet = workbook.Sheets[sheetName];
            var html = XLSX.utils.sheet_to_html(sheet);
            // Mettre à jour le contenu du modal avec l'aperçu
            document.getElementById('excel-preview').innerHTML = html;
        })
        .catch(error => console.error('Erreur lors du chargement du fichier Excel :', error));
}

function loadPdfFile(filePath) {
    document.getElementById('pdf-preview').src = filePath;
    var pdfModal = new bootstrap.Modal(document.getElementById('pdfModal'));
    pdfModal.show();
}


