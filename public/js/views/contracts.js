/**
 * Created by anarela on 10-02-17.
 */
var theGrid = null;
var theGrid2 = null;
var isEdit = false;
var url = null;
var select_account_code = null;
var select_account_year = null;
var select_area = null;
var edit_accounts = 'false';
var _edit_contract = false;
var _select_contract_number = null;
var cuentas_validas = null;

if(typeof consultaCuentas != 'undefined'){
    url = consultaCuentas;
}
if(typeof consulta == 'undefined'){
    consulta = '';
}
if(typeof edit_contract != 'undefined'){
    _edit_contract = edit_contract;
}

var initialize = function(){
    $('#account_detail .paso-2').attr('disabled','disabled');
    $('#account_detail .paso-2').addClass('opacity');
};

var cleanAddAccount = function(){
    $('#account_area').val('');
    $('#account_code').val('');
    $('#account_year').val('');
};

$(document).ready(function(){

    _select_contract_number = $("#contract_number").val();
    
    var proveedores = null;

    $("#guardar").on("click",function(e){

        var id_contract = $("#contract_number").val();
        var provider = $("#id_provider").val();

        if($('#is_active').is(':checked') && $('#end_date').val()!= null && $('#end_date').val()!= ""){

            var end = $("#end_date").val().split("/");
            var endDate = new Date(end[2], end[1]-1, end[0]);
            var today = new Date();

            today.setHours(0,0,0,0);

            if(today>endDate){
                console.log('Fecha de finalización menor que la fecha de activación');
                $('#errorActivacion').removeAttr('hidden');
                return false;
            }else{
                $('#errorActivacion').hide();
            }
        }

        $("#f_contrato").validator({
            custom: {
                'containsProvider': function () {
                    if($.inArray($('#id_provider').val(), proveedores) == -1){
                        return "Debe seleccionar un proveedor de la lista";
                    }
                }
            }
        });

        var tableRow = $("#cuentasContrato tr td").filter(function() {
            return $(this).text() == "No hay datos disponibles";
        }).parent('tr');

        $("#accounts").val("");
        if(tableRow.length == 0){
            var ini = 1;
            $("#cuentasContrato tr td").each(function() {
                $this = $(this);

                var content = "";
                if($this.index()==0){
                    content = (ini != 1 ? ',' : '[') + '{ "area":"' + $this.text() ;
                }else if($this.index() ==1){
                    content= '", "account_code":"' + $this.text();
                }else if($this.index() ==2){
                    content= '", "account_year":"' + $this.text()+ '" }';
                }
                $("#accounts").val($("#accounts").val() + content);
                ini = ini +1;
            });

            var final =  $("#accounts").val() + ']';
            console.log(final);
            console.log(JSON.parse(final));
            $("#accounts").val(JSON.stringify(JSON.parse(final)));
            $("#edit_accounts").val(edit_accounts);
        }else{
            console.log("No hay detalles");
            $("#accounts").val(JSON.stringify(JSON.parse("[]")));
        }

        //validaciones especificas
        var split_path = location.pathname.split('/');
        var type_show = split_path[1];

        validateContract(id_contract,provider,type_show);
    });

    //Limpia el campo
    $('#contract_number').keyup(function(){
        $(this).css('border-color','');
        $("#contract_number_errors").empty();
    });


    $('#contract_number').on('keypress', function (event) {
        //console.log(event.which);
        OrdenesCompraLib.preventInput(event);
    });

    $('#description').on('keypress', function (event) {
        //console.log(event.which);
        OrdenesCompraLib.preventInput(event);
    });

    $( "#contract_number" ).bind( 'paste',function()
    {
        OrdenesCompraLib.replaceCharacters('contract_number');
    });

    $( "#description" ).bind( 'paste',function()    {

        OrdenesCompraLib.replaceCharacters('description');
    });

    //Datatable cuentas contrato
    theGrid2 = $('#cuentasContrato').DataTable({
        "processing": true,
        "serverSide": true,
        "ordering": false,
        "responsive": true,
        "searching": false,
        "ajax": url,
        "bPaginate": false,
        "paging":   false,
        "info" :false,
        "aoColumns": [
            { "sType": "string" ,  "sClass":"center-column" },
            { "sType": "string" ,  "sClass":"center-column codeAccount" },
            { "sType": "string" ,  "sClass":"left-column nameAccount" },
        ],
        "columnDefs": [
            {
                "render": function ( data, type, row ) {
                    if(_edit_contract){
                        return '<a onclick="return editBudget(\''+ row[0] +'\',\''+ row[1] + '\',\'' + row[2] + '\',\'' + row[3] + '\')" ><i class="icon-pencil"></i></a>';
                    }else{
                        return '';
                    }
                },
                "targets": 3 ,
                "orderable": false
            },
        ]
    });

    initialize();

    $("#contract_pdf").fileinput({
        language: "es",
        allowedFileExtensions: ["pdf"],
        showUploadedThumbs: false,
        showUpload: false
    });

    $('.daterange-single').daterangepicker({
        singleDatePicker: true,
        locale: {
            format: 'DD/MM/YYYY'
        },
        autoUpdateInput: false
    });

    $('.daterange-single').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('DD/MM/YYYY'));
    });
    
    theGrid = $('#thegrid').DataTable({
        "processing": true,
        "serverSide": true,
        "ordering": true,
        "responsive": true,
        "ajax": consulta,
        "aoColumns": [
            { "sType": "string" },
            { "sType": "string", "bSearchable": true, "bSortable": true, "bVisible": true },
            { "sType": "string", "bSearchable": true, "bSortable": true, "bVisible": true },
            { "sType": "string", "bVisible": true, "sClass":"center-column" },
            { "sType": "string", "bSearchable": true, "bSortable": true, "bVisible": true , "sClass":"center-column"},
            { "sType": "string", "bVisible": true, "sClass":"center-column", "bVisible": false },
            { "sType": "string", "sClass":"center-column" },
            { "sType": "string", "sClass":"center-column" },
            { "sType": "string", "sClass":"center-column" },
        ],
        "columnDefs": [
            {
                "visible": false,
                "targets": 0 ,
                "orderable": false
            },
            {
                "render": function ( data, type, row ) {
                    return '<a href="'+contratos+'/'+row[0]+'">'+data+'</a>';
                },
                "targets": 1
            },
            {
                "render": function ( data, type, row ) {
                    var msj = '<i class="fa fa-square-o fa-lg"></i>';
                    if(row[3]==1){
                        msj = '<i class="fa fa-check-square-o fa-lg"></i>';
                    }
                    return msj;
                },
                "targets": 3,
                "orderable" : false
            },
            {
                "visible": false,
                "targets": 5 ,
                "orderable": false
            },
            {
                "render": function ( data, type, row ) {
                    var value = "-";
                    if(row[5]!= null && row[5] != ""){
                        value = '<a href="pdfContratos/'+row[5]+'" target="_blank" ><i class="fa fa-file-pdf-o fa-lg"></i></a>';
                    }
                    return value;
                },
                "targets": 6,
                "orderable" : false,
                "visible" : verPDF
            },
            {
                "render": function ( data, type, row ) {
                    return '<a href="'+contratos+'/'+row[0]+'/edit" ><i class="icon-pencil"></i></a>';

                },
                "targets": 7,
                "orderable" : false,
                "visible" : editar
            },
            {
                "render": function ( data, type, row ) {
                    return '<a href="#" onclick="return doDelete('+row[0]+')" ><i class="icon-trash"></i></a>';
                },
                "targets": 7+1,
                "orderable" : false,
                "visible" : eliminar
            },
        ]
    });

    var substringMatcher = function(strs) {
        return function findMatches(q, cb) {
            var matches, substringRegex;

            // an array that will be populated with substring matches
            matches = [];

            // regex used to determine if a string contains the substring `q`
            substrRegex = new RegExp(q, 'i');

            // iterate through the pool of strings and for any string that
            // contains the substring `q`, add it to the `matches` array
            $.each(strs, function(i, str) {
                if (substrRegex.test(str)) {
                    matches.push(str);
                }
            });
            cb(matches);
        };
    };

    $.ajax({
        url:   '/getProvidersContracts',
        type:  'get',
        success:  function (response) {
            proveedores = response;

            $('.typeahead').typeahead({
                    minLength: 0,
                    highlight: true
                },
                {
                    name: 'providers',
                    limit: 20,
                    source: substringMatcher(response)
                });

            $('.typeahead').bind('typeahead:select', function(ev, suggestion) {
                console.log('Selection: ' + suggestion);
            });
        }
    });

    //Agregar cuentas
    $('#add_account').click(function(){

        isEdit = false;

        $("#mensaje_error_detalle").empty();

        $('#account_detail .paso-2').attr('disabled',false);
        $('#account_detail .paso-2').removeClass('opacity');

        $('#account_area').val('');
        $('#account_code').val('');
        $('#account_year').val('');
        $("#add_account_detail").attr('data-action','true');
    });

    $("#account_area").change(function(){

        $("#account_code").val("");
        $("#account_year").val("");

        $('#account_code').typeahead('destroy');
        $.ajax({
            url:   '/getAccountsArea',
            type:  'get',
            data:{
                id_area:$("#account_area").val(),
                year:$(this).val()
            },
            success:  function (response) {
                cuentas_validas = response;

                $('#account_code').typeahead({//cambiar nombre clase
                        minLength: 0,
                        highlight: true
                    },
                    {
                        name: 'cuentas',
                        limit: 20,
                        source: substringMatcher(response)
                    });

                $('#account_code').bind('typeahead:select', function(ev, suggestion) {
                    console.log('Selection: ' + suggestion);
                    getInformationAccount($("#account_area").val(),suggestion);

                });
            }
        });

    });

    //Agregar cuenta
    $('#add_account_detail').click(function(){

        if($(this).attr('disabled') == 'disabled'){
            return false;
        }

        if($("#account_area").val() == ""){
            $("#mensaje_error_detalle").html('<p style="color:#D84315">Debe seleccionar un área</p>');
            return false;
        }

        if($.inArray($('#account_code').val(), cuentas_validas) == -1){
            $("#mensaje_error_detalle").html('<p style="color:#D84315">Debe seleccionar una cuenta de la lista</p>');
            return false;
        }

        if($("#add_account_detail").attr('data-action')=='false'){
            return false;
        }

        $("#mensaje_error_detalle").empty();
        var path_contract = window.location.pathname;
        var action_path = path_contract.split('/');

        if(action_path[2] != 'create'){
            edit_accounts = 'true';
        }

        var account_area = $('#account_area option:selected').text();
        var account_code= $('#account_code').val();
        var account_year = $('#account_year').val();

        var area_duplicada = false;

        //Buscamos si el área ya esta agregada en la tabla
        $('#cuentasContrato tr').find('td').each(function() {
            if( account_area == $(this).text() && (isEdit == false || (isEdit == true && account_area != select_area)) ){
                $("#mensaje_error_detalle").html('<p style="color:#D84315">Ya hay una cuenta para esta área</p>');
                area_duplicada = true;
                return false;
            }else{
                $("#mensaje_error_detalle").empty();
            }
        });

        if(area_duplicada){
            return false;
        }

        var edit= '<a onclick="return editBudget(\''+ account_area +'\',\''+ account_code + '\', \'' + account_year + '\', \'edit\')" ><i class="icon-pencil"></i></a>'
        var row = '<tr><td class="center-column">'+ account_area +'</td><td class="center-column codeAccount">'+ account_code +'</td>' +
            '<td class="left-column nameAccount" style="max-width:112px; word-wrap:break-word;">'+ account_year+'</td>'+
            '<td >'+ edit +'</td></tr>'

        if(validate(isEdit)) {

            if(isEdit == false){

                $('#cuentasContrato .dataTables_empty').remove();
                $('#cuentasContrato tr:last').after(row);
            }else{

                var tableRow = $("#cuentasContrato tr td").filter(function() {
                    return ($(this).text() == select_area && $(this).next("td").text() == select_account_code && $(this).next("td").next("td").text() == select_account_year);
                }).parent('tr');

                tableRow.replaceWith(row);
                isEdit = false;
            }

            cleanAddAccount();
            $("#add_account_detail").attr('data-action','false');
            initialize();
        }
    });

    //Valida las cuentas
    var validate = function(isEdit){

        var anio = $('#account_year').val();
        var code = $('#account_code').val();
        if( code == "" ){
            $("#mensaje_error_detalle").html('<p style="color:#D84315">Ingrese un código de cuenta válido</p>');
            return false;
        }

        if(anio == ""|| anio.length < 4 ){
            $("#mensaje_error_detalle").html('<p style="color:#D84315">Ingrese un año válido </p>');
            return false;
        }
        return true;
    }
});

function editBudget(area, account_code, year,edit) {

    if(edit == "noedit"){
        sweetAlert("No se puede editar esta cuenta", "Esta cuenta tiene asociadas Ordenes de Compra!", "error");
        cleanAddAccount();
        initialize();
        return false;
    }

    $("#mensaje_error_detalle").empty();

    $('#add_account').click();

    $('[name=account_area] option').filter(function() {
        return ($(this).text() == area);
    }).prop('selected', true).trigger('change');

    $('#account_code').val(account_code);
    $('#account_year').val(year);

    select_account_code = account_code ;
    select_account_year = year;
    select_area = area;

    var path_contract = window.location.pathname;
    var action_path = path_contract.split('/');

    if(action_path[2] != 'create'){
        edit_accounts = 'true';
    }

    isEdit = true;

    return false;
}

function getInformationAccount(id_area, account_code){
    var year = new Date().getFullYear();

    $.ajax({
        url:   '/getInformationAccount',
        type:  'get',
        data:{
            id_area:id_area,
            account_code:account_code,
            year:year
        },
        success:  function (response) {
            $('#account_year').val(response.budget_year);
        }
    });
}

function validateContract(contract_number,name_provider,type_show){
    var validate = true;
    $.ajax({
        url:   '/contracts/validateContract',
        type:  'get',
        data:{
            contract_number:contract_number,
            name_provider:name_provider
        },
        success:  function (response) {
            //var respuesta = $.parseJSON(response);
            //debugger;
            var response_contract_number = "";
            if(response.contract_number != undefined){
                response_contract_number = response.contract_number;
            }

            //Validamos si son iguales para verificar si existe
            if(type_show == 'create'){
                if(response_contract_number == contract_number){
                    validate = false;
                }
            }else{
                if(_select_contract_number != contract_number && response_contract_number == contract_number){
                    validate = false;
                }
            }

            if(validate){
                $('#f_contrato').submit();
            }else{
                $('#contract_number').css('border-color','red');
                $('#contract_number').focus();
                $('html, body').animate({
                    scrollTop: ($("#contract_number").offset().scroll)
                }, 500);
                $('#contract_number_errors').html('<span style="color: red">Este numero de contrato ya existe</span>')
            }
        }
    });
}

function doDelete(id) {
    var url = contratos + '/' + id;
    var tok = $("input[name=_token]").val();

    OrdenesCompraLib.confirmDelete(url,id,tok,theGrid);
    return false;
}