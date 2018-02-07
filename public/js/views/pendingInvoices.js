var theGrid = null;
$(document).ready(function(){

    theGrid = $('#thePendingInvoices').DataTable({
        "processing": true,
        "serverSide": true,
        "ordering": true,
        "responsive": true,
        "ajax": consulta,
        "aoColumns": [
            { "sType": "string", "bSearchable": true, "bSortable": true },
            { "sType": "string", "bSearchable": true, "bSortable": true },
            { "sType": "string", "bSearchable": true, "bSortable": true, "bVisible": true, "sClass":"right-column" },
            { "sType": "string", "bSearchable": true, "bSortable": true, "bVisible": true, "sClass":"right-column" },
            { "sType": "string", "bSearchable": true, "bSortable": true, "bVisible": true, "sClass":"center-column" },
            { "sType": "string",  "sClass":"center-column" },
        ],
        "columnDefs": [
            {
                "render": function ( data, type, row ) {
                    return '<a href="'+detalle+'/'+row[5]+'">'+data+'</a>';
                },
                "targets": 0
            },
            {
                "render": function ( data, type, row ) {
                    return '<a href="'+facturas+'/'+row[5]+'" ><i class="icon-cart-add"></i></a>';
                },
                "targets": 5 ,
                "orderable": false ,
                "visible" : asignar }
        ]
    });

});

