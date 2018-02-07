
var url = null;
var isEdit = false;
var montoDisp = 0;
var montoInicial = 0;
var montoInicialCuentas = 0;
var montoDispCuentas = 0;
$(document).ready(function() {

    $('[data-toggle="tooltip"]').tooltip();

    $("#guardar").on("click",function(){

        $('#total_budget_html').val(obtenerTotalPorArea());

        $("#f_areas").validator({
            custom: {
                'contains': function () {

                    if($.inArray($('#manager_name').val(), opcionesUsuarios) == -1){
                        return "Debe seleccionar un usuario de la lista";
                    }else{
                        let id_user="";
                        $.each(getUsuarios, function(key, val) {
                            if($('#manager_name').val() == val.user_name){
                                id_user = val.id_user;
                                return false;
                            }
                        });
                        $('#id_user').val(id_user);
                    }
                }
            }
        });

        var tableRow = $("#montosAreas tr td").filter(function() {
            return $(this).text() == "No hay datos disponibles";
        }).parent('tr');

        $("#budgets").val("");
        if(tableRow.length == 0){
            var ini = 1;
            $("#montosAreas tr td").each(function() {
                $this = $(this);

                var content = "";
                if($this.index()==0){
                    content = (ini != 1 ? ',' : '[') + '{ "year":"' + $this.text() ;
                }else if($this.index() ==1){
                    content= '", "code":"' + $this.text();
                }else if($this.index() ==2){
                    content= '", "name":"' + $this.text();
                }else if($this.index() ==3){
                    content= '", "desc":"' + $this.text();
                }else if($this.index() ==4){
                    content= '", "amount":"' + $this.text().replace('$ ', '') + '" }';
                }
                $("#budgets").val($("#budgets").val() + content);

                ini = ini +1;
            });

            var final =  $("#budgets").val() + ']';
            // console.log(final);
            // console.log(JSON.parse(final));
            $("#budgets").val(JSON.stringify(JSON.parse(final)));
        }else{
            // console.log("No hay detalles");
            $("#budgets").val(JSON.stringify(JSON.parse("[]")));
        }


    });

    var obtenerTotalPorArea = function(){

        var monto = 0;
        $("#montosAreas tr td.initial").each(function() {
            var montoCuenta = $(this).text().replace('$', '');
            montoCuenta = montoCuenta.replace(/\./g,'');
            monto = monto + parseFloat(montoCuenta);
        });

        return monto;
    }


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
        url:   '/areas/getUsers',
        type:  'get',
        success:  function (response) {
            let nombres = [];
            let a = 0;
            $.each(response, function(key, val) {
                nombres[a] = val.user_name;
                a++;
            });
            opcionesUsuarios = nombres;
            getUsuarios = response;
            $('.typeahead').typeahead({
                    minLength: 0,
                    highlight: true
                },
                {
                    name: 'users',
                    limit: 20,
                    source: substringMatcher(nombres)
                });
        }
    });

    $("#budget_closed").change(function(){
        if( $(this).is(':checked') ){

            var tableRow = $("#montosAreas tr td").filter(function() {
                return $(this).text() == "No hay datos disponibles";
            }).parent('tr');

            if(tableRow.length != 0) {
                toastr.options = {
                    "closeButton": true,
                    "progressBar": true,
                    "preventDuplicates": true,
                    "showDuration": "2000",
                    "hideDuration": "2000",
                    "timeOut": "9000",
                    "extendedTimeOut": "1000",
                };
                toastr["error"]("Está cerrando el presupuesto sin haber agregado ninguna cuenta.", "Se fijará el presupuesto del área en cero ($ 0)");
            }

        }
    });


    if(typeof consultaPresupuesto != 'undefined'){
        url = consultaPresupuesto;



        theGrid2 = $('#montosAreas').DataTable({
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
                { "sType": "string" ,  "sClass":"center-column", "sWidth": "5%"},
                { "sType": "string" ,  "sClass":"center-column codeAccount", "sWidth": "5%"},
                { "sType": "string" ,  "sClass":"left-column nameAccount" , "sWidth": "32%"},
                { "sType": "string",  "sClass":"left-column descAccount" , "sWidth": "32%"},
                { "sType": "string",  "sClass":"right-column initial" , "sWidth": "15%"},
                { "sType": "string",  "sClass":"right-column notAssigned" , "sWidth": "15%"},
                { "sType": "string",  "sClass":"center-column ", "sWidth": "5%" },
            ],
            "columnDefs": [
                {
                    "visible":true,
                    "targets": 0
                },
                {
                    "render": function ( data, type, row ) {
                        var budget = row[4].replace('$', '');
                        budget = budget.replace(/\./g,'');
                        var budgetNotAssigned = row[5].replace('$', '');
                        budgetNotAssigned = budgetNotAssigned.replace(/\./g,'');
                        return '<a href="#" onclick="return editBudget('+ row[0] +','+ budget +',\''+ row[1] + '\',\'' + row[2] + '\',\'' + row[3]+'\',\'' + budgetNotAssigned+'\')" ><i class="icon-pencil"></i></a>';
                    },
                    "targets": 6 ,
                    "orderable": false
                },
            ],
            "fnInitComplete": function(oSettings, json) {
                if($("#is_closed").val() == 1){
                    getBudgetAvailableByArea($('#year_budget').val());
                    updateFreeAmmount();
                }
            }
        });

        var updateFreeAmmount = function(){
            var montoLibre = montoInicial - obtenerTotalPorArea();
            $("#total_free").val(montoLibre.toLocaleString( 'de-DE' ));
            if(montoLibre > 0){
                $("#total_free_div").addClass('has-success');
                $("#icon-alert").show();
            }else{
                $("#total_free_div").removeClass('has-success');
                $("#icon-alert").hide();
            }

        }

        var initialize = function(){

            $('#budget_detail .paso-2').attr('disabled','disabled');
            $('#budget_detail .paso-2').addClass('opacity');

        };

        function getBudgetAvailableByArea(anio){

            var path = '/getBudget/' + idArea + '/' + anio;
            return $.ajax({
                url: path,
                type: 'get',
                async: false,
                beforeSend: function() {
                    $('#loading').modal('toggle');
                },
                success: function (response) {
                    var disp = response.total_budget_available;
                    montoDisp = parseFloat(disp);

                    var ini = response.total_budget_initial;
                    montoInicial = parseFloat(ini);
                    $('#loading').modal('hide');
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    $('#loading').modal('hide');
                    let reason = new Error(errorThrown);
                    throw reason;
                }
            });
        }

        function getBudgetAvailableByAccount(anio, code){

            var path = '/getBudgetAccount/' + idArea + '/' + anio + '/' + code;
            return $.ajax({
                url: path,
                type: 'get',
                async: false,
                beforeSend: function() {
                    $('#loading').modal('toggle');
                },
                success: function (response) {
                    // console.log(response);
                    var disp = response.total_budget_available;
                    montoDispCuentas = parseFloat(disp);

                    var ini = response.total_budget_initial;
                    montoInicialCuentas = parseFloat(ini);
                    $('#loading').modal('hide');
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    $('#loading').modal('hide');
                    let reason = new Error(errorThrown);
                    throw reason;
                }
            });
        }

        $('#add_budget').click(function(){

            $("#mensaje_error_detalle").empty();

            $('#budget_detail .paso-2').attr('disabled',false);
            $('#budget_detail .paso-2').removeClass('opacity');

            $('#initial_budget').val('');
            $('#account_code').val('');
            $('#account_name').val('');
            $('#description').val('');

            isEdit = false;

        });

        var refreshBudgetNotAssigned = function(newBudget, anio, code){

            var tableRow = $("#montosAreas tr td").filter(function() {
                return ($(this).text() == anio && $(this).next("td").text() == code);
            }).parent('tr');

            var initial = tableRow.find(".initial").html();
            initial = initial.replace('$', '');
            initial = parseFloat(initial.replace(/\./g,''));

            var notAssigned = tableRow.find(".notAssigned").html();
            notAssigned = notAssigned.replace('$', '');
            notAssigned = parseFloat(notAssigned.replace(/\./g,''));

            newBudget = parseFloat(newBudget);

            if(newBudget>initial){
                notAssigned = notAssigned + (newBudget - initial);
            }else{
                notAssigned = notAssigned - (initial - newBudget);
            }

            return notAssigned;
        };

        $('#initial_budget').maskMoney({prefix:'$ ', allowNegative: false, allowZero:true, thousands:'.', decimal:',', affixesStay: false,precision: 0});

        initialize();

        var validate = function(isEdit){

            var anio = $('#year_budget').val();
            var code = $('#account_code').val();

            if(anio == "" || anio.length < 4){
                $("#mensaje_error_detalle").html('<p style="color:#D84315">Ingrese un año válido</p>');
                return false;
            }

            if( code == "" ){
                $("#mensaje_error_detalle").html('<p style="color:#D84315">Ingrese un código de cuenta válido</p>');
                return false;
            }

            if($('#initial_budget').val() == "" || ($('#initial_budget').val() == 0 && isEdit == false)){
                $("#mensaje_error_detalle").html('<p style="color:#D84315">Ingrese un presupuesto válido</p>');
                return false;
            }

            var tableRow = $("#montosAreas tr td").filter(function() {
                return ($(this).text() == anio && $(this).next("td").text() == code);
            }).parent('tr');

            var monto= $('#initial_budget').val().replace(/\./g,'');
            monto = parseFloat(monto);

            if(isEdit == true){
                //Obtener el presupuesto disponible
                getBudgetAvailableByArea(anio);
            }

            if($("#is_closed").val() == 1){
                var montoActualTabla = obtenerTotalPorArea();
                var montoTotalPermitidoPorCuentas = 0;
                if(isEdit == true){
                    var initial = tableRow.find(".initial").html();
                    initial = initial.replace('$', '');
                    initial = parseFloat(initial.replace(/\./g,''));
                    montoTotalPermitidoPorCuentas = (montoActualTabla - initial + monto);
                }else{
                    montoTotalPermitidoPorCuentas = (montoActualTabla + monto);
                }

                if( montoTotalPermitidoPorCuentas > montoInicial ){
                    $("#mensaje_error_detalle").html('<p style="color:#D84315">El monto excede el presupuesto máximo asignado al área (' + montoInicial.toLocaleString( 'de-DE' ) + ')</p>');
                    return false;
                }
            }

            if(isEdit == false){

                if(tableRow.length >= 1){
                    $("#mensaje_error_detalle").html('<p style="color:#D84315">Ya existe un presupuesto para esa cuenta y año</p>');
                    $('#initial_budget').val('');
                    $('#account_code').val('');
                    $('#account_name').val('');
                    $('#description').val('');
                    initialize();
                    return false;
                }

            }

            if(isEdit==true & idArea != undefined){

                getBudgetAvailableByAccount(anio, code);

                var initial = tableRow.find(".initial").html();
                initial = initial.replace('$', '');
                initial = parseFloat(initial.replace(/\./g,''));

                var notAssigned = tableRow.find(".notAssigned").html();
                notAssigned = notAssigned.replace('$', '');
                notAssigned = parseFloat(notAssigned.replace(/\./g,''));

                if(montoInicialCuentas > monto) {
                    if (  montoDispCuentas  < (montoInicialCuentas - monto )) {
                        var montoMsj=  notAssigned.toLocaleString( 'de-DE' );
                        var montoMaximoReducible = montoInicialCuentas - montoDispCuentas;
                        montoMaximoReducible = montoMaximoReducible.toLocaleString( 'de-DE' );
                        $("#mensaje_error_detalle").html('<p style="color:#D84315">El presupuesto inicial no puede ser reducido en un monto mayor a lo actualmente disponible en la cuenta (' + montoMsj + '). El mínimo monto aceptado es '+ montoMaximoReducible+'.</p>');
                        $('#initial_budget').val(initial);
                        $('#initial_budget').focus();
                        return false;
                    }
                }
            }
            return true;
        }

        $('#add_budget_detail').click(function(e) {

            if ($('#initial_budget').prop('disabled')) {
                e.preventDefault();
            } else {

                $("#mensaje_error_detalle").empty();

                var anio = $('#year_budget').val();
                var budget = $('#initial_budget').val();
                var code = $('#account_code').val();
                var name = $('#account_name').val();
                var desc = $('#description').val();

                var edit = '<a href="#" onclick="return editBudget(' + anio + ', ' + budget.replace(/\./g, '') + ', \'' + code + '\', \'' + name + '\', \'' + desc + '\')" ><i class="icon-pencil"></i></a>';
                var row = '<tr><td class="center-column" >' + anio + '</td><td class="center-column codeAccount">' + code + '</td>' +
                    '<td class="left-column nameAccount" >' + name + '</td>' +
                    '<td class="left-column descAccount" >' + desc + '</td>' +
                    '<td class="right-column initial" >$ ' + budget + '</td><td class="right-column notAssigned" > $ ' + 'MONTO_NO_ASIGNADO' + '</td>' +
                    '<td class="center-column" >' + edit + '</td></tr>';

                if (validate(isEdit)) {
                    if (isEdit == false) {
                        $('#montosAreas .dataTables_empty').remove();
                        row= row.replace('MONTO_NO_ASIGNADO', budget.toLocaleString( 'de-DE' ));
                        $('#montosAreas tr:last').after(row);
                    } else {

                        var newNotAssigned = refreshBudgetNotAssigned(budget.replace(/\./g, ''), anio, code);

                        var tableRow = $("#montosAreas tr td").filter(function () {
                            return ($(this).text() == anio && $(this).next("td").text() == code);
                        }).parent('tr');

                        row= row.replace('MONTO_NO_ASIGNADO', newNotAssigned.toLocaleString( 'de-DE' ));
                        tableRow.replaceWith(row);

                        isEdit = false;
                    }

                    $('#initial_budget').val('');
                    $('#account_code').val('');
                    $('#account_name').val('');
                    $('#description').val('');

                    if ($("#is_closed").val() == 1) {
                        updateFreeAmmount();
                    }

                    initialize();
                }
            }
        });

        $('input[name="year_budget"]').keypress(function() {
            if (this.value.length >= 4) {
                return false;
            }
        });


    }

});


function editBudget(id, budget, code, name, desc) {

    $("#mensaje_error_detalle").empty();

    $('#add_budget').click();
    $('#year_budget').attr('disabled','disabled');
    $('#year_budget').val(id);
    $('#initial_budget').val(budget);
    $('#account_code').attr('disabled','disabled');
    $('#account_code').val(code);
    $('#account_name').val(name);
    $('#description').val(desc);
    $('#initial_budget').focus();


    isEdit = true;

    return false;

}
