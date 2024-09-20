$(document).ready(function() {
    // Obtenir la date actuelle
    const today = new Date();

    // Formater la date en YYYY-MM-DD (format attendu par les input date)
    const formattedDate = today.toISOString().split('T')[0];
    
    // Affecter la date actuelle au champ input
    document.getElementById('date_saisie').value = formattedDate;
    document.getElementById('date_operation').value = formattedDate;
});