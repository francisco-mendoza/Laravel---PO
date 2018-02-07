
$(document).ready(function(){
    $.extend( true, $.fn.dataTable.defaults, {
        "language": {
            "lengthMenu": " _MENU_  Registros por página",
            "processing": "Cargando...",
            "zeroRecords": "No hay resultados",
            "info": "Página _PAGE_ de _PAGES_",
            "infoEmpty": "No hay registros disponibles",
            "infoFiltered": "(filtrados del _MAX_ total de registros)",
            searchPlaceholder: "Introduzca texto",
            search: "Buscar:",
            "paginate": {
                "previous": "Anterior",
                "next": "Siguiente"
            },
            "emptyTable": "No hay datos disponibles"
        },

    } );

    

    // Switchery
    // ------------------------------

    // Initialize multiple switches
    if (Array.prototype.forEach) {
        var elems = Array.prototype.slice.call(document.querySelectorAll('.switchery-default '));
        elems.forEach(function(html) {
            var switchery = new Switchery(html)
        });

        var elems = Array.prototype.slice.call(document.querySelectorAll('.switchery-primary'));
        elems.forEach(function(html) {
            var switchery = new Switchery(html, { color: '#2196F3' })
        });

        var elems = Array.prototype.slice.call(document.querySelectorAll('.switchery-info'));
        elems.forEach(function(html) {
            var switchery = new Switchery(html, { color: '#00BCD4' })
        });
    }
    else {
        var elems = document.querySelectorAll('.switchery');
        for (var i = 0; i < elems.length; i++) {
            var switchery = new Switchery(elems[i]);
        }
    }


    // Checkboxes/radios (Uniform)
    // ------------------------------

    // Default initialization
    $(".styled, .multiselect-container input").uniform({
        radioClass: 'choice'
    });

    // File input
    $(".file-styled").uniform({
        wrapperClass: 'bg-blue',
        fileButtonHtml: '<i class="icon-file-plus"></i>'
    });

});

