var theGrid = null;
var opcionesUsuarios;
var getUsuarios;

const RolGerencia = 2;

$(document).ready(function(){


    
    theGrid = $('#thegrid').DataTable({
        "processing": true,
        "serverSide": true,
        "ordering": true,
        "responsive": true,
        "ajax": consulta,
        "aoColumns": [
            { "sType": "string" },
            { "sType": "string", "bSearchable": true, "bSortable": true },
            { "sType": "string", "bSearchable": true, "bSortable": true, "bVisible": true },
            { "sType": "string", "bSearchable": true, "bSortable": true, "bVisible": true },
            { "sType": "string", "bSearchable": true, "bSortable": true, "bVisible": true },
            { "sType": "string",  "sClass":"center-column" },
            { "sType": "string",  "sClass":"center-column" },
        ],
        "columnDefs": [
            {
                "visible": false,
                "targets": 0 ,
                "orderable": false
            },
            {
                "render": function ( data, type, row ) {
                    return '<a href="'+areas+'/'+row[0]+'">'+data+'</a>';
                },
                "targets": 1
            },
            {
                "render": function ( data, type, row ) {
                    return '<a href="'+areas+'/'+row[0]+'/edit" ><i class="icon-pencil"></i></a>';
                },
                "targets": 5 ,
                "orderable": false ,
                "visible" : editar },
            {
                "render": function ( data, type, row ) {
                    return '<a href="#" onclick="return doDelete('+row[0]+')" ><i class="icon-trash"></i></a>';
                },
                "targets": 5+1,
                "orderable": false,
                "visible" : eliminar
            }
        ]
    });
    
});


function doDelete(id) {
    var url = areas + '/' + id;


    var tok = $("input[name=_token]").val();

    OrdenesCompraLib.confirmDelete(url,id,tok,theGrid);


    return false;
}
