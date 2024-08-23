
$(document).ready(function () {
    $("#basic-datatables").DataTable({
            "pageLength": 5,
            "language": {
                    "lengthMenu": "Afficher _MENU_ éléments par page",
                    "zeroRecords": "Aucun élément trouvé",
                    "info": "Affichage de l'élément _START_ à _END_ sur _TOTAL_ éléments",
                    "infoEmpty": "Aucun élément disponible",
                    "infoFiltered": "(filtré à partir de _MAX_ éléments au total)",
                    "search": "Rechercher :",
                    "paginate": {
                        "first": "Premier",
                        "last": "Dernier",
                        "next": "Suivant",
                        "previous": "Précédent"
                    }
            }
    });
  });