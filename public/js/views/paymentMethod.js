/**
 * Created by anarela on 03-02-17.
 */
var theGrid = null;
$(document).ready(function(){

    $("#guardar").on("click",function(){
        $("#f_methods").validator();
    });


    if(typeof consulta != 'undefined') {
        theGrid = $('#thegrid').DataTable({
            "processing": true,
            "serverSide": true,
            "ordering": true,
            "responsive": true,
            "ajax": consulta,
            "aoColumns": [
                { "sType": "string" },
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
                        return '<a href="'+metodos+'/'+row[0]+'">'+data+'</a>';
                    },
                    "targets": 1
                },
                {
                    "render": function ( data, type, row ) {
                        return '<a href="'+metodos+'/'+row[0]+'/edit" ><i class="icon-pencil"></i></a>';
                    },
                    "targets": 2,
                    "orderable": false ,
                    "visible" : editar },
                {
                    "render": function ( data, type, row ) {
                        return '<a href="#" onclick="return doDelete('+row[0]+')"><i class="icon-trash"></i></a>';
                    },
                    "targets": 2+1,
                    "orderable": false,
                    "visible" : eliminar
                },
            ]
        });
    }
    
});
function doDelete(id) {
    var url = metodos + '/' + id;


    var tok = $("input[name=_token]").val();

    OrdenesCompraLib.confirmDelete(url,id,tok,theGrid);


    return false;
}
