var theGrid = null;
$(document).ready(function(){

    $("#guardar").on("click",function(){
        $("#f_usuario").validator();
        
    });

    if(typeof consulta != 'undefined') {

        theGrid = $('#thegrid').DataTable({
            "processing": true,
            "serverSide": true,
            "ordering": true,
            "responsive": true,
            "ajax": consulta,
            "aoColumns": [
                {"sType": "numeric", "bSearchable": true, "bSortable": true, "bVisible": false},
                {"sType": "string", "bSearchable": true, "bSortable": true, "bVisible": true},
                {"sType": "string", "bSearchable": true, "bSortable": true, "bVisible": true},
                {"sType": "string", "bSearchable": false, "bSortable": false, "bVisible": true},
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
                        return '<a href="' + usuarios + '/' + row[0] + '">' + data + '</a>';
                    },
                    "targets": 1
                },
                {
                    "render": function (data, type, row) {
                        var result = row[3] == null ? '<div class="media-left media-middle btn bg-indigo-400 btn-rounded btn-icon btn-xs"><i class="icon-user"></i></div>' :
                            '<img class="img-circle img-xs" src="' + row[3] + '" alt="">';
                        return result;
                    },
                    "targets": 3,
                    "orderable": false
                },
                {
                    "render": function (data, type, row) {
                        return '<a href="' + usuarios + '/' + row[0] + '/edit" ><i class="icon-pencil"></i></a>';
                    },
                    "targets": 6,
                    "orderable": false,
                    "visible": editar
                },
                {
                    "render": function (data, type, row) {
                        return '<a href="#" onclick="return doDelete(' + row[0] + ')" ><i class="icon-trash"></i></a>';
                    },
                    "targets": 6 + 1,
                    "orderable": false,
                    "visible": eliminar
                },
            ]
        });
    }

});
function doDelete(id) {

    var url = usuarios + '/' + id;

    var tok = $("input[name=_token]").val();

    OrdenesCompraLib.confirmDelete(url,id,tok,theGrid);

    return false;
}
