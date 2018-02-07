var theGrid = null;
var url = null;
var listaIconos;
$(document).ready(function(){

    //-------- Datatable -----------//
    if(typeof consulta != 'undefined'){
        url = consulta
    }

    if(typeof consulta != 'undefined') {


        theGrid = $('#thegrid').DataTable({
            "processing": true,
            "serverSide": true,
            "ordering": true,
            "responsive": true,
            "ajax": url,
            "aoColumns": [
                {"sType": "string"},
                {"sType": "string", "bSearchable": true, "bSortable": true, "bVisible": true},
                {"sType": "string", "bSearchable": true, "bSortable": true, "bVisible": true},
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
                        return '<a href="' + menu_options + '/' + row[0] + '">' + data + '</a>';
                    },
                    "targets": 1
                },
                {
                    "render": function (data, type, row) {
                        var split_icon = data.split(' ');
                        var key_icon = "";
                        if (split_icon[0] == 'fa') {
                            key_icon = 'fa';
                        }
                        return '<i class="' + key_icon + ' ' + data + '"></i>  ' + data + '';
                    },
                    "targets": 4,
                },
                {
                    "render": function (data, type, row) {
                        return '<a href="' + menu_options + '/' + row[0] + '/edit" class=""><i class="icon-pencil"></i></a>';
                    },
                    "targets": 5, "orderable": false, "visible": editar
                },
                {
                    "render": function (data, type, row) {
                        return '<a href="#" onclick="return doDelete(' + row[0] + ')" class=""><i class="icon-trash"></i></a>';
                    },
                    "targets": 5 + 1,
                    "orderable": false,
                    "visible": eliminar
                },
            ]
        });
    }
    //--------------------------------//

    //-------- Create ---------------//
    $.ajax({
        url:   '/fontawesome',
        type:  'get',
        success:  function (response) {
            listaIconos = response;

        }
    });

    $("#guardar").on("click",function(){

        $("#f_menu_options").validator({
            custom: {
                'contains': function () {
                    //debugger;
                    var option_menu_input = $('#option_icon').val();
                    var count = 0;
                    var iconos = [];
                    $.each(listaIconos, function(key, value) {
                        iconos[count] = value.key;
                        count++;
                    });
                    var split_option_menu_input = option_menu_input.split(' ');

                    if(typeof split_option_menu_input[1] != 'undefined'){
                        if($.inArray(split_option_menu_input[1], iconos) == -1){
                            return "Debe seleccionar un icono de la lista";
                        }
                    }
                }
            }
        });
    });

    var fontAwesomeIcons = new Bloodhound({
        datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value'),
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        prefetch: '/fontawesome',
        remote: {
            url: '/fontawesome',
            wildcard: '%QUERY'
        }
    });

    $('.typeahead').typeahead({
        minLength: 0,
        highlight: true
    },
    {
        name: 'Iconos',
        display: 'key',
        limit:584,
        source: fontAwesomeIcons,
        templates: {
            empty: [
                '<div class="empty-message">',
                'unable to find any Best Picture winners that match the current query',
                '</div>'
            ].join('\n'),
            suggestion: Handlebars.compile('<div><i class="fa {{key}}" aria-hidden="true"></i> {{value}} </div>')
        }
    });


    //--------------------------------//
});
function doDelete(id) {



    var url = menu_options + '/' + id;


    var tok = $("input[name=_token]").val();

    OrdenesCompraLib.confirmDelete(url,id,tok,theGrid);

    return false;
}
