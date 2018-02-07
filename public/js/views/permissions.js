var theGrid = null;
$(document).ready(function(){

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
                {"sType": "string"},
                {"sType": "string"},
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
                        return '<a href="' + permissions + '/' + row[0] + '">' + data + '</a>';
                    },
                    "targets": 1
                },
                {
                    "render": function (data, type, row) {
                        return '' + data + '';
                    },
                    "targets": 2
                },
                {
                    "render": function (data, type, row) {
                        return '' + data + '';
                    },
                    "targets": 3
                },
                {
                    "render": function (data, type, row) {
                        return '<a href="' + permissions + '/' + row[0] + '/edit" class=""><i class="icon-pencil"></i></a>';
                    },
                    "targets": 4,
                    "orderable": false,
                    "visible": editar
                },
                {
                    "render": function (data, type, row) {
                        return '<a href="#" onclick="return doDelete(' + row[0] + ')" ><i class="icon-trash"></i></a>';
                    },
                    "targets": 4 + 1,
                    "orderable": false,
                    "visible": eliminar
                },
            ]
        });
    }

});

function doDelete(id) {
    var url = permissions + '/' + id;


    var tok = $("input[name=_token]").val();

    OrdenesCompraLib.confirmDelete(url,id,tok,theGrid);


    return false;
}