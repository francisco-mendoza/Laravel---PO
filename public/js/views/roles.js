var theGrid = null;
$(document).ready(function(){

    // Multiple selection
    var list_box = $('.list-permissions').bootstrapDualListbox({
        preserveSelectionOnMove: 'all',
        moveOnSelect: false,
        infoText: 'Mostrando {0} permisos',
        infoTextFiltered: '<span class="label label-info">Filtrados</span> {0} de {1}',
        infoTextEmpty: '',
        filterPlaceHolder: 'Buscar permiso',
        filterTextClear: 'Mostrar todos',
        selectorMinimalHeight: 300, //Altura lista
        nonSelectedListLabel: '<h6 class="label label-danger">Permisos No Asignados</h6>',
        selectedListLabel: '<h6 class="label label-success">Permisos Asignados</h6>',
    });

    if(typeof consulta != 'undefined') {
        theGrid = $('#thegrid').DataTable({
            "processing": true,
            "serverSide": true,
            "ordering": true,
            "responsive": true,
            "ajax": consulta,
            "aoColumns": [
                {"sType": "string"},
                {"sType": "string", "bSearchable": true, "bSortable": true, "bVisible": true},
                {"sType": "string", "sClass": "center-column"},
                {"sType": "string", "sClass": "center-column"},
            ],
            "columnDefs": [
                {
                    "visible": false,
                    "targets": 0,
                    "orderable": false
                },
                {
                    "render": function (data, type, row) {
                        return '<a href="' + roles + '/' + row[0] + '">' + data + '</a>';
                    },
                    "targets": 1
                },
                {
                    "render": function (data, type, row) {
                        return '<a href="' + roles + '/' + row[0] + '/edit" class=""><i class="icon-pencil"></i></a>';
                    },
                    "targets": 2,
                    "orderable": false,
                    "visible": editar
                },
                {
                    "render": function (data, type, row) {
                        return '<a href="#" onclick="return doDelete(' + row[0] + ')" ><i class="icon-trash"></i></a>';
                    },
                    "targets": 2 + 1,
                    "orderable": false,
                    "visible": eliminar
                },
            ]
        });
    }

});
function doDelete(id) {
    var url = roles + '/' + id;


    var tok = $("input[name=_token]").val();

    OrdenesCompraLib.confirmDelete(url,id,tok,theGrid);

    return false;
}