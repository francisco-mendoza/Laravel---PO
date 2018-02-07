var theOrdersGrid = null;
var theApprovedOrdersGrid = null;


$(document).ready(function(){

    var esVisibleArea = false;
    var esVisibleUsuario = false;
    var esVisibleEstado = false;
    var order = "";



    if(!esUsuarioRegular){ //Rol Finanzas y Gerencia
        esVisibleArea = true;
        esVisibleUsuario = true;
        $('#tablaOrdenesAprobadas').removeAttr('hidden');
        $('#titulo').removeAttr('hidden');
    }else{
        esVisibleEstado = true;
    }

    // if(esVisibleUsuario){
    //     order = [1, 'asc'];
    // }



    theOrdersGrid = $('#ordersGrid').DataTable({
        "processing": true,
        "serverSide": true,
        "ordering": true,
        "responsive": true,
        "ajax": consulta,
        "aoColumns": [
            { "sType": "string", "bSortable": false },
            { "sType": "string", "bSearchable": true, "bSortable": true },
            { "sType": "string", "bSearchable": true, "bSortable": true },
            { "sType": "string", "bSearchable": true, "bSortable": true, "bVisible": true },
            { "sType": "string", "bSearchable": true, "bSortable": true, "bVisible": true, "sClass":"right-column" },
            { "sType": "string", "bSearchable": true, "bSortable": true, "bVisible": true , "sClass":"center-column none-padding"},
            { "sType": "string", "bVisible": true, "sClass":"center-column" },
            { "sType": "string", "sClass":"center-column" },
            { "sType": "string", "sClass":"center-column" },
        ],
        "columnDefs": [
            {

                "searchable" :false,
                "className": '',
                "targets": 0,
                "visible": esVisibleUsuario,
                "render": function (data, type, row){
                    var result = "";
                    if(row[6] != "Rechazada"){
                        result = '<input type="radio" name="idEmitidas[]" value="'
                            + $('<div/>').text(data).html() + '">';
                    }
                    return result;
                },
                "orderable": false
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
                    return '<a href="'+detail+'/'+ row[3] +'">'+data+'</a>';
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
                    return '<a href="'+detail+'/'+ row[3] +'" ><i class="fa fa-search-plus fa-lg"></i></a>';
                },
                "targets": 7 ,
                "orderable": false                   },
            {
                "render": function ( data, type, row ) {
                    return '<a href="'+detail+'/'+ row[3] +'/print'+'" ><i class="fa fa-print fa-lg"></i></a>';
                },
                "targets": 7+1,
                "orderable": false,
                "visible": permisoImprimir
            },
        ],
        select: {
            style:    'os',
            selector: 'td:first-child'
        },
        language: {
            select: {
                rows: "%d orden seleccionada"
            },
            "info": "Mostrando _START_ hasta _END_ de _TOTAL_ registros",
            "infoFiltered": " - filtrados de _MAX_ registros",
        },
        'order' : []

    });

    $('#ordersGrid_filter input').unbind() // Unbind previous default bindings
        .bind("input", function(e) { // Bind our desired behavior
            // If the length is 3 or more characters, or the user pressed ENTER, search
            if(this.value.length >= 3 ) {
                // Call the API search function
                theOrdersGrid.search(this.value).draw();
            }
            // Ensure we clear the search if they backspace far enough
            if(this.value == "") {
                theOrdersGrid.search("").draw();
            }
            return;
        });


    theApprovedOrdersGrid = $('#approvedOrdersGrid').DataTable({
        "processing": true,
        "serverSide": true,
        "ordering": true,
        "responsive": true,
        "ajax": consultaAprobadas,
        "aoColumns": [
            { "sType": "string", "bVisible": true , "bSortable": false , "sClass" : "" },
            { "sType": "string", "bSearchable": true, "bSortable": true },
            { "sType": "string", "bSearchable": true, "bSortable": true },
            { "sType": "string", "bSearchable": true, "bSortable": true, "bVisible": true },
            { "sType": "string", "bSearchable": true, "bSortable": true, "bVisible": true, "sClass":"right-column" },
            { "sType": "string", "bSearchable": true, "bSortable": true, "bVisible": true, "sClass":"center-column none-padding" },
            { "sType": "string", "bSearchable": true, "bSortable": true, "bVisible": true, "sClass":"center-column" },
            { "sType": "string", "sClass":"center-column" },
            { "sType": "string", "sClass":"center-column" }
        ],
        "columnDefs": [
            {
                "searchable" :false,
                "targets": 0,
                "render": function (data, type, row){
                    var result = "";
                    if(row[6] != "Rechazada"){
                        result = '<input type="radio" name="idAprobadas[]" value="'
                            + $('<div/>').text(data).html() + '">';
                    }
                    return result;
                },
                "orderable": false
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
                    return '<a href="'+detail+'/'+ row[3] +'">'+data+'</a>';
                },
                "targets": 3
            },
            {
                "sClass": "dt-left",
                "targets": 4
            },
            {
                "visible": true,
                "targets": 6,
                "orderable": true
            },
            {
                "render": function ( data, type, row ) {
                    return '<a href="'+detail+'/'+ row[3] +'" ><i class="fa fa-search-plus fa-lg"></i></a>';
                },
                "targets": 7 ,
                "orderable": false                   },
            {
                "render": function ( data, type, row ) {
                    return '<a href="'+detail+'/'+ row[3] +'/print'+'" ><i class="fa fa-print fa-lg"></i></a>';
                },
                "targets": 7+1,
                "orderable": false,
                "visible": permisoImprimir
            },
        ],
        select: {
            style:    'os',
            selector: 'td:first-child'

        },
        language: {
            select: {
                rows: "%d orden seleccionada"
            },
            "info": "Mostrando _START_ hasta _END_ de _TOTAL_ registros",
            "infoFiltered": " - filtrados de _MAX_ registros",
        },
        'order' : []
    });


    $('#approvedOrdersGrid_filter input').unbind() // Unbind previous default bindings
        .bind("input", function(e) { // Bind our desired behavior
            // If the length is 3 or more characters, or the user pressed ENTER, search
            if(this.value.length >= 3 ) {
                // Call the API search function
                theApprovedOrdersGrid.search(this.value).draw();
            }
            // Ensure we clear the search if they backspace far enough
            if(this.value == "") {
                theApprovedOrdersGrid.search("").draw();
            }
            return;
        });

    var validarEliminacion = function(nombre){
        
        var idOrden = "";        

        if(nombre == "eliminarEmitidas"){
            if (!$("#ordersGrid input[name='idEmitidas[]']:checked").val()) {
                swal("Atención!", "No ha seleccionado una Orden de Compra.");
                return false;
            }
            idOrden = $("#ordersGrid input[name='idEmitidas[]']:checked").val();
        }

        if(nombre == "eliminarAprobadas"){
            if (!$("#approvedOrdersGrid input[name='idAprobadas[]']:checked").val()) {
                swal("Atención!", "No ha seleccionado una Orden de Compra.");
                return false;
            }
            idOrden = $("#approvedOrdersGrid input[name='idAprobadas[]']:checked").val();
        }

        var url = "/deletePurchaseOrder" ;

        swal({
                title: "Está seguro?",
                text: "No se podrá recuperar la Orden de Compra seleccionada!",
                type: "warning",
                showCancelButton: true,
                cancelButtonText: "Cancelar",
                confirmButtonColor: "#4CAF50",
                confirmButtonText: "Eliminar!",
                closeOnConfirm: false
            },
            function(){
                $.ajax({
                    url: url,
                    type: 'GET',
                    data: {
                        "idOrden": idOrden
                    },
                    success: function ()
                    {
                        if(nombre == "eliminarEmitidas") {
                            theOrdersGrid.ajax.reload();
                        } else{
                            theApprovedOrdersGrid.ajax.reload();
                        }
                        swal("Eliminado!", "La orden de compra ha sido eliminada.", "success");
                    },
                    error: function(errorThrown) {
                        var permisos = errorThrown.responseJSON['message'];
                        swal("Error!", "La orden de compra NO ha sido eliminada. " + (permisos !== null ? permisos: "" ), "error");
                    }

                });
            });
    };

    var validarEdicion = function(nombre){
        var idOrden = "";

        if(nombre == "editarEmitidas"){
            if (!$("#ordersGrid input[name='idEmitidas[]']:checked").val()) {
                swal("Atención!", "No ha seleccionado una Orden de Compra.");
                return false;
            }
            idOrden = $("#ordersGrid input[name='idEmitidas[]']:checked").val();
        }

        if(nombre == "editarAprobadas"){
            if (!$("#approvedOrdersGrid input[name='idAprobadas[]']:checked").val()) {
                swal("Atención!", "No ha seleccionado una Orden de Compra.");
                return false;
            }
            idOrden = $("#approvedOrdersGrid input[name='idAprobadas[]']:checked").val();
        }
        window.location.href="editarOrden/"+idOrden;
    };

    $("#eliminarEmitidas").on("click",function(e) {
        validarEliminacion(this.id);
    });

    $("#eliminarAprobadas").on("click",function(e) {
        validarEliminacion(this.id);
    });

    $("#editarEmitidas").on("click",function(){
        validarEdicion(this.id);
    });

    $("#editarAprobadas").on("click",function(){
        validarEdicion(this.id);
    });
});

