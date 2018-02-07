
var theGrid = null;
$(document).ready(function(){

    theGrid = $('#thegrid').DataTable({
        "processing": true,
        "serverSide": true,
        "ordering": true,
        "responsive": true,
        "ajax": consulta,
        "aoColumns": [
            { "sType": "string", "bSearchable": true, "bSortable": true },
            { "sType": "string", "bSearchable": true, "bSortable": true },
            { "sType": "string", "bSearchable": true, "bSortable": true, "bVisible": true, "sClass":"right-column" },
            { "sType": "string",  "sClass":"center-column" },
            { "sType": "string",  "sClass":"center-column" },
        ],
        "columnDefs": [
            {
                "render": function ( data, type, row ) {
                    return '<a href="'+facturas+'/'+row[3]+'">'+data+'</a>';
                },
                "targets": 0
            },
            {
                "render": function ( data, type, row ) {
                    return '<a href="'+facturas+'/'+row[3]+'/edit" ><i class="icon-pencil"></i></a>';
                },
                "targets": 3 ,
                "orderable": false ,
                "visible" : editar },
            {
                "render": function ( data, type, row ) {
                    return '<a href="#" onclick="return doDelete('+"'"+row[3]+"'"+')" ><i class="icon-trash"></i></a>';
                },
                "targets": 3+1,
                "orderable": false,
                "visible" : eliminar
            }
        ]
    });

});

function doDelete(id) {
    var url = facturas + '/' + id;

    var tok = $("input[name=_token]").val();

    OrdenesCompraLib.confirmDelete(url,id,tok,theGrid);


    return false;
}
