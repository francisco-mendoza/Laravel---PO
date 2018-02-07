var theFilteredOrdersGrid = null;


$(document).ready(function(){

    var esVisibleArea = true;
    var esVisibleUsuario = true;
    var esVisibleEstado = true;

    var detailRows = []; //sub row stuff


    theFilteredOrdersGrid = $('#filteredOrdersGrid').DataTable({
        "processing": true,
        "serverSide": true,
        "ordering": true,
        "responsive": true,
        "ajax": consulta,
        "dom": '<"top"f><"float-right"l>rt<"bottom"ip><"clear">',
        "aoColumns": [
            {  "bSortable": false },
            { "sType": "string", "bSearchable": true, "bSortable": true },
            { "sType": "string", "bSearchable": true, "bSortable": true },
            { "sType": "string", "bSearchable": true, "bSortable": true, "bVisible": true },
            { "sType": "string", "bSearchable": true, "bSortable": true, "bVisible": true, "sClass":"left-column" },
            { "sType": "string", "bSearchable": true, "bSortable": true, "bVisible": true, "sClass":"right-column" },
            { "sType": "string", "bSearchable": true, "bSortable": true, "bVisible": true , "sClass":"center-column none-padding"},
            { "sType": "string", "bVisible": true, "sClass":"center-column" },
        ],
        "columnDefs": [
            {

                // "searchable" :false,
                "className": 'details-control',
                "targets": 0,
                "data" : null,
                "visible": true,
                "orderable": false,
                "defaultContent": ''
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
                "targets": 5
            },
            {
                "visible": esVisibleEstado,
                "targets": 7,
                "orderable": true
            },
        ],
        language: {
            "searchPlaceholder": "Nombre de proveedor o descripción de Orden de Compra",
            search: "Introduzca un parámetro de búsqueda:",
            "info": "Mostrando _START_ hasta _END_ de _TOTAL_ registros",
            "infoFiltered": " - filtrados de _MAX_ registros",
        },
        'order' : []

    });

    $('#filteredOrdersGrid tbody').on('click','td.details-control', function(){
        var tr = $(this).closest('tr');
        var idx = $.inArray(tr.attr('id'), detailRows );
        var row = theFilteredOrdersGrid.row( tr );
        var idOrder = tr.find('td:eq(3)').text();
        $.ajax({
            type:'GET',
            url: '/getDetailInfo/'+idOrder,
            data: { id : idOrder},
            dataType: 'json',
            success: function(result){
                if (row.child.isShown()){
                    tr.removeClass('shown');
                    row.child.hide();
                    // remove from the 'open' array
                    detailRows.splice(idx,1); //??
                }
                else {
                    tr.addClass('shown');
                    row.child(format(result)).show();
                    //add to the open array
                    if (idx=== -1){
                        detailRows.push(tr.attr('id'));
                    }
                }

            }
        });

    }); //end of click function

    function format ( details ){
        if(details.length === 0){ return "<td>No hay detalles asociados</td>"};
        var i = 0;
        var len = details.length;
        var out;
        out = '<div><p><strong>Resumen del detalle de la Orden de Compra seleccionada:</strong></p></div></br>' +
            '<table cellpadding="5" cellspacing="10" border="0" class="table table-condensed table-bordered " style="padding-left:50px; border-top: 1px solid #ddd !important;">' +
            '<thead><tr><th class="center-column" width="80px">Cantidad</th><th class="center-column" width="200px">Descripción</th>' +
            '<th class="center-column" width="150px">Monto</th></tr></thead>';
        for(;i<len; i++){
            out += '<tr>'+
                '<td class="center-column">'+ details[i].quantity +'</td>'+
                '<td>'+details[i].description+'</td>'+
                // '<td class="right-column"> '+parseFloat(details[i].price).toLocaleString( 'de-DE' )+'</td>'+
                '<td class="right-column"> '+details[i].price+'</td>'+
                '</tr>';
        }
        out += '</table>';
        return out;
    };


    $('.dataTables_filter').addClass('pull-left');
    $('.dataTables_filter input').addClass('search-big-box');

    $('.dataTables_filter input').unbind() // Unbind previous default bindings
        .bind("input", function(e) { // Bind our desired behavior
            // If the length is 3 or more characters, or the user pressed ENTER, search
            if(this.value.length >= 3 ) {
                // Call the API search function
                theFilteredOrdersGrid.search(this.value).draw();
            }
            // Ensure we clear the search if they backspace far enough
            if(this.value == "") {
                theFilteredOrdersGrid.search("").draw();
            }
            return;
        });

    //Patron se llena cuando el usuario va al detalle y regresa a la búsqueda
    if(patron != ""){
        $('.dataTables_filter input').val(patron);
        theFilteredOrdersGrid.search( patron ).draw();
    }
    
    
});
