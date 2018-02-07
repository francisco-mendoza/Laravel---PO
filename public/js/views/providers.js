/**
 * Created by anarela on 09-02-17.
 */
var theGrid = null;
$(document).ready(function(){

    $("#guardar").on("click",function(){
        $("#f_provider").validator();
    });

    $('#name_provider').on('keypress', function (event) {
        //console.log(event.which);
        OrdenesCompraLib.preventInput(event);
    });

    $( "#name_provider" ).bind( 'paste',function()
    {
        OrdenesCompraLib.replaceCharacters('name_provider');
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
                {"sType": "string", "bSearchable": true, "bSortable": true},
                {"sType": "string", "bSearchable": true, "bSortable": true, "bVisible": true},
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
                        return '<a href="' + proveedor + '/' + row[0] + '">' + data + '</a>';
                    },
                    "targets": 1
                },
                {
                    "render": function (data, type, row) {
                        return '<a href="' + proveedor + '/' + row[0] + '/edit" ><i class="icon-pencil"></i></a>';
                    },
                    "targets": 4,
                    "orderable": false,
                    "visible": editar
                },
                {
                    "render": function (data, type, row) {
                        return '<a href="#" onclick="return doDelete(' + row[0] + ')"><i class="icon-trash"></i></a>';
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
    var url = proveedor + '/' + id;


    var tok = $("input[name=_token]").val();

    OrdenesCompraLib.confirmDelete(url,id,tok,theGrid);

    return false;
}
