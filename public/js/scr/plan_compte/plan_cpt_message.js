const appendAlertMessage = (message, type) => {
    var placementFrom = 'top';
    var placementAlign = 'right';
    var state = type;
    var style = 'plain';
    var content = {};

    content.message = message;
    content.title = "Notification";
    content.icon = "none";

    $.notify(content, {
        type: state,
        placement: {
            from: placementFrom,
            align: placementAlign,
        },
        time: 1000,
        delay: 0,
    });
}

// Cette fonction sera appelée pour afficher les messages flash
function displayFlashMessages(messages) {
    for (const [type, messageList] of Object.entries(messages)) {
        for (const message of messageList) {
            appendAlertMessage(message, 'warning');
        }
    }
}

// Si vous voulez que les messages s'affichent automatiquement au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    if (typeof flashMessages !== 'undefined') {
        displayFlashMessages(flashMessages);
    }
});