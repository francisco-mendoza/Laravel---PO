/**
 * Created by anarela on 08-02-17.
 */
var theOrdersGrid = null;

$(document).ready(function(){

    var esVisibleArea = false;
    var esVisibleUsuario = true;
    var esVisibleEstado = false;


    if(esUsuarioOwner){ //Rol Finanzas
        esVisibleArea = true;
    }

    theOrdersGrid = $('#approveOrders').DataTable({
        "processing": true,
        "serverSide": true,
        "ordering": true,
        "responsive": true,
        "ajax": consulta,
        "aoColumns": [
            {},
            { "sType": "string", "bSearchable": true, "bSortable": true },
            { "sType": "string", "bSearchable": true, "bSortable": true },
            { "sType": "string", "bSearchable": true, "bSortable": true, "bVisible": true },
            { "sType": "string", "bSearchable": true, "bSortable": true, "bVisible": true, "sClass":"right-column" },
            { "sType": "string", "bSearchable": true, "bSortable": true, "bVisible": true, "sClass":"center-column"},
            { "sType": "string",  "sClass":"center-column" },
            { "sType": "string",  "sClass":"center-column" },
        ],
        "columnDefs": [
            {
                "visible": false,
                "targets": 0 ,
                "orderable": true
            },

            {
                "visible": esVisibleArea,
                "targets": 1 ,
                "orderable": true
            },
            {
                "visible": esVisibleUsuario,
                "targets": 2 ,
                "orderable": true
            },
            {
                "render": function ( data, type, row ) {
                    // return '<a href="'+areas+'/'+row[2]+'">'+data+'</a>';
                    return '<a href="'+detail+'/'+ row[3] +'/validate'+'">'+data+'</a>';
                },
                "targets": 3
            },
            {
                "sClass": "dt-left",
                "targets": 4
            },
            {
                "visible": esVisibleEstado,
                "targets": 6,
                "orderable": false
            },
            {
                "render": function ( data, type, row ) {
                    // return '<a href="'+areas+'/'+row[2]+'/edit" ><i class="fa fa-search-plus fa-lg"></i></a>';
                    return '<a href="'+detail+'/'+ row[3] +'/validate'+'" ><i class="fa fa-thumbs-o-up fa-lg"></i></a>';
                },
                "targets": 7 ,
                "orderable": false,
                "visible" : permisoAprobar
            },
        ],
        language: {
            "info": "Mostrando _START_ hasta _END_ de _TOTAL_ registros",
            "infoFiltered": " - filtrados de _MAX_ registros",
        }
    });
    
});
