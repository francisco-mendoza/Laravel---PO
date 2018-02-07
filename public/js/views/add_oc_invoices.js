const PesoChileno   = 2;
const Dolar         = 3;
const Euro          = 4;
const UnidadFomento = 5;
const CoronaNoruega = 6;
const CoronaSueca   = 7;

var theBillingOrdersGrid = null;
var theSelectedOrdersGrid = null;


$(document).ready(function() {

    //Mostrar o no la selección de órdenes de forma automática
    if(orders.length == 0){
        $('#addOC_div').show();
    }

    OrdenesCompraLib.maskMoney(id_currency); //Inicializar los campos con la moneda de la factura

    /** -------------------- */
    /**  Inicializar pasos */
    /** -------------------- */
    var form = $(".steps-basic").show();

    // Basic wizard setup
    $(".steps-basic").steps({
        headerTag: "h6",
        bodyTag: "fieldset",
        transitionEffect: "fade",
        titleTemplate: '<span class="number">#index#</span> #title#',
        labels: {
            next: 'Siguiente <i class="icon-arrow-right14 position-right"></i>',
            previous: '<i class="icon-arrow-left13 position-left"></i> Anterior',
            finish: 'Asignar Ordenes de Compra <i class=" icon-add-to-list position-right"></i>'
        },
        onStepChanging: function (event, currentIndex, newIndex) {

            // Allways allow previous action even if the current form is not valid!
            if (currentIndex > newIndex) {
                return true;
            }


            // Needed in some cases if the user went back (clean up)
            if (currentIndex < newIndex) {

                // To remove error styles
                form.find(".body:eq(" + newIndex + ") label.error").remove();
                form.find(".body:eq(" + newIndex + ") .error").removeClass("error");
            }

            if(currentIndex===0){ //Voy a generar data de OC
                $('.steps-basic').find('.actions .disabled').show();
                displaySelectedPurchaseOrders();
            }

            return validateSelection();

        },
        onStepChanged: function(event, currentIndex, newIndex){
            if(currentIndex===0) { //Voy a generar data de OC
                $('.steps-basic').find('.actions .disabled').hide();
            }
        },
        onFinished: function (event, currentIndex) {
            $("#f_invoice").trigger('submitted');
        }
    });

    $('.steps-basic').find('.actions .disabled').hide();

    var validateSelection = function(){

        var len = theBillingOrdersGrid.rows( { selected: true } ).data().length;

        if(len > 0){
            var arrayAssignedOrdersIds = orders.map(function(ord) {return ord.id_purchase_order;});
            var ordersSelected = theBillingOrdersGrid.rows( { selected: true } ).data();
            for(var i=0; i<len; i++){
                var obj= ordersSelected[i];
                if($.inArray(obj[1], arrayAssignedOrdersIds) >= 0){
                    $("#message_error_selection").html('<label class="validation-error-label" >Por favor deseleccione las Ordenes de Compra que ya estaban previamente asignadas para continuar.</label>');
                    return false;
                }
            }
        }

        $("#message_error_selection").empty();
        return true;
    }

    var validateOrders= function(){
        var ordersValid=true;
        var totalValid = true;
        var totalOrders = 0;
        $('input[name^="subtotal"]').each(function(){
            var subtotal = OrdenesCompraLib.filterMoney(document.getElementById($(this).attr("name")).value);
            if(subtotal == 0 || subtotal == '' || isNaN(subtotal)){
                ordersValid = false;
                $(this).addClass('with-error');
                $(this).keyup(function(){
                    $(this).removeClass('with-error');
                    $("#mensaje_error_detalle").empty();
                });
            }else{
                totalOrders = totalOrders + parseFloat(subtotal);
            }
        });

        $('input[name^="rate"]').each(function(){
            var rate = document.getElementById($(this).attr("name")).value;
           // var p = rate.indexOf(".");
            rate = rate.replace(',','.');
            rate = parseFloat(rate);
            if(rate === 0 || rate === '' || isNaN(rate)){
                    ordersValid = false;
                    $(this).addClass('with-error');
                    $(this).keyup(function(){
                        $(this).removeClass('with-error');
                        $("#mensaje_error_detalle").empty();
                    });
            }
        });

        if(totalOrders > OrdenesCompraLib.filterMoney($("#total_bill").text())){
            totalValid = false;
            // $("#total_bill").addClass('with-error');
            $("#icon_total_bill").show();
            $("#total_bill_group").addClass('text-warning-800');
            $("#selectedOrdersGrid .money").keyup(function(){
                // $("#total_bill").removeClass('with-error');
                $("#total_bill_group").removeClass('text-warning-800');
                $("#icon_total_bill").hide();
                $("#mensaje_error_detalle").empty();
            });
        }

        return {ordersValid: ordersValid, totalValid: totalValid};
    }

    var createConfirmationMessage = function(){
        let message = "<span>Según lo que especificaste se asignarán a la factura las siguientes órdenes de compra:</span><br /><br />";
        let showMsg = false;
        let totalSubtotal = 0;
        message = message + "<div style='overflow-y: auto; max-height: 267px;'><table class='table table-striped table-framed table-condensed' style='width:100%'><thead> <tr> <th></th> <th style='width:33.3%;'> Nro Orden </th> <th style='width:33.3%;'> Pendiente Facturar </th> <th style='width:33.3%;'> Total a Facturar </th> </tr></thead><tbody>";
        theSelectedOrdersGrid.rows().every( function ( rowIdx, tableLoop, rowLoop ) {
            var data = this.data();
            var alert = "";

            var calculado = document.getElementById('calculated['+data[0].trim()+']').value;
            var subtotal = document.getElementById('subtotal['+data[0].trim()+']').value;
            var bill = parseFloat(OrdenesCompraLib.filterMoney(data[1]));
            var currency_sign = ' ' + data[1].split(' ')[1];


            calculado = parseFloat(OrdenesCompraLib.filterMoney(calculado));
            subtotal = parseFloat(OrdenesCompraLib.filterMoney(subtotal));
            if(calculado > bill){
                showMsg = true;
                alert = '<i class="icon-bubble-notification position-right text-warning-400"></i>';
            }
            totalSubtotal = totalSubtotal + subtotal;
            message = message + "<tr><td>"+alert+"</td><td style='text-align:left;'>"+ data[0]+ "</td><td class='right-column'>"+ $.number(bill, 2, ',', '.') + currency_sign +"</td><td class='right-column'>"+ $.number(calculado, 2, ',', '.') + currency_sign +"</td></tr>";


        } );
        message = message + "</tbody></table><br />";
        if(showMsg){
            message = message + "<span>Las órdenes de compra que estén identificadas con <i class='icon-bubble-notification text-warning-400'></i> se estarían sobrefacturando.</span>";
        }

        message= message + "<br /><div ><table class='table table-striped table-framed table-condensed full-width' ><thead> <tr> <th style='width:50%;' class='text-center'> Monto Factura </th> <th style='width:50%;' class='text-center'> Monto Consumido Factura </th>  </tr></thead><tbody>";
        message = message + "<tr><td class='right-column'>"+ $('#total_bill').text() +"</td><td class='right-column'>"+ $.number(totalSubtotal, 2, ',', '.') + " " + $('#total_bill').text().split(' ')[1] +"</td></tr>";
        message = message + "</tbody></table></div><br /></div>";

        return message;
    }

    /** -------------------- */
    /**  Submit Facturas */
    /** -------------------- */
    $('#f_invoice').on('submitted', function() {

        var ordersValidation= validateOrders();


        if(!ordersValidation.ordersValid){
            $("#mensaje_error_detalle").html('<label class="validation-error-label" >Complete los campos en rojo. Debe distribuir el monto total de la factura entre cada Orden de Compra:</label>');
        }
        else if(!ordersValidation.totalValid) {
            $("#mensaje_error_detalle").html('<label  class="validation-error-label" >Los subtotales por Orden de Compra no pueden sobrepasar el monto total de la factura</label>');
        }else{ //Todo esta bien
            $("#mensaje_error_detalle").empty();
            swal({
                    title: "Está seguro?",
                    text: createConfirmationMessage(),
                    type: "warning",
                    html: true,
                    showCancelButton: true,
                    confirmButtonColor: "#4CAF50",
                    confirmButtonText: "Si, guardar facturar!",
                    cancelButtonText: "No, necesito volver!",
                    customClass: "",
                    closeOnConfirm: false,
                    closeOnCancel: false
                },
                function(isConfirm){
                    if (isConfirm) {
                        $('.confirm').prop('disabled',true);

                        $("#f_invoice").submit();
                    } else {
                        swal("Cancelado", "Edita e intenta nuevamente!", "error");
                    }
                });

            $(".sweet-alert").css({"width":"700", "transform": "translateX(-50%)", "margin-left": "auto"});
        }



    });


    var detailRows = []; //sub row stuff


    /** ---------------------------- */
    /**  Tabla de Ordenes a Facturar */
    /** ---------------------------- */
    theBillingOrdersGrid = $('#billingOrdersGrid').DataTable({
        "processing": true,
        "serverSide": true,
        "ordering": true,
        "responsive": true,
        "ajax": consulta,
        "dom": '<"top">rt<"bottom"i><"float-right"l><"clear">',
        "aoColumns": [
            {  "bSortable": false },
            {  "bSortable": false },
            { "sType": "string", "bSearchable": true, "bSortable": true, "bVisible": true },
            { "sType": "string", "bSearchable": true, "bSortable": true, "bVisible": true },
            { "sType": "string", "bSearchable": true, "bSortable": true, "bVisible": true, "sClass":"right-column" },
            { "sType": "string", "bSearchable": true, "bSortable": true, "bVisible": true, "sClass":"right-column" },
        ],
        "columnDefs": [
            {
                "searchable" :false,
                "className": 'select-checkbox',
                "targets": 0,
                "visible": true,
                "orderable": false,
                "defaultContent": ''
            },
            {
                "searchable" :false,
                "className": 'details-control',
                "targets": 1,
                "data" : 'detail',
                "visible": true,
                "orderable": false,
                "defaultContent": ''
            }
        ],
        rowId: 'detail',
        select: {
            style: 'multi',
            selector: 'td:first-child'
        },
        language: {
            "searchPlaceholder": "Nombre de proveedor o descripción de Orden de Compra",
            search: "Introduzca un proveedor:",
            "info": "Mostrando _START_ hasta _END_ de _TOTAL_ registros",
            "infoFiltered": " - filtrados de _MAX_ registros",
            select: {
                rows: {
                    _: "%d ordenes seleccionadas",
                    0: "No hay ordenes seleccionadas",
                    1: "1 orden seleccionada"
                }
            }
        },
        'order' : []

    });

    var addRowToSelectedOrdersGrid = function(id_order, forBilling,value_rate, disabled_rate, class_total){


        var nameInput = "subtotal[" + id_order + "]" ;
        var rateInput = "rate[" + id_order + "]" ;
        var totalInput = "calculated[" + id_order + "]" ;
        var inputSubtotalBill = '<input type="text" id='+nameInput+' name='+nameInput+' class="form-control money full-width text-right moneyValid exchange_rate '+nameInput+' " >';
        var inputExchangeRate = '<input type="text" id='+rateInput+' name='+rateInput+' '+disabled_rate+' value="'+value_rate+'" class="form-control full-width text-center exchange_rate" >';
        var inputTotalCalculated = '<input type="text" id='+totalInput+' name='+totalInput+' class=" input-label full-width text-right '+class_total+'" readonly><input type="hidden" id="currency_symbol_'+id_order+'" value="'+forBilling.split(' ')[1]+'">' +
            '<div id="icon_'+totalInput+'" class="input-group-addon input-label" style="display: none;float:right"><i class="fa fa-spinner fa-spin fa-lg fa-fw"></i></div>';
        theSelectedOrdersGrid.row.add([id_order, forBilling, inputSubtotalBill,inputExchangeRate, inputTotalCalculated]).draw( false );

    };

    var addAssignatedPurchaseOrders = function(){

        var total_bill = $("#total_bill").text();
        var total_bill_currency = total_bill.split(' ')[1];

        if(orders.length > 0){

            $.each( orders, function( index, value ){
                var rate = value.exchange_rate;
                var currency_oc = value.disp.split(' ')[1];
                addRowToSelectedOrdersGrid(value.id_purchase_order,value.disp, rate, total_bill_currency === currency_oc ? 'readonly' : '', 'money_'+value.id_purchase_order);
                var sub_total = value.subtotal_po_currency.split(' ');

                var total_calculated = 0;
                var sub_total_i = 0;
                if(document.getElementById('currency_symbol_'+value.id_purchase_order).value  === '$'){
                    total_calculated = sub_total[0].split(',')[0];
                }else{
                    total_calculated = sub_total[0];
                }
                
                if(total_bill_currency === '$'){
                    sub_total_i = value.subtotal.split(',')[0];
                }else{
                    sub_total_i = value.subtotal;
                }

                console.log(total_bill_currency);
                document.getElementById('subtotal['+value.id_purchase_order +']').value = sub_total_i;
                document.getElementById('calculated['+value.id_purchase_order +']').value = total_calculated;
                OrdenesCompraLib.maskMoneyBySymbol(sub_total[1],'money_'+value.id_purchase_order);
            });
        }
        $('input[name^="rate"]').on('keypress', restrictCharacter);
    };

    var addEventToCalculateByExchangeRate = function(){
        //Vemos la oc
        var this_id = $(this).attr('id').split('[')[1];
        this_id = this_id.replace(']','');
        var this_val = $(this).val();

        var id_this_calculated = 'calculated['+this_id+']';
        var id_this_subtotal = 'subtotal['+this_id+']';
        var id_icon = 'icon_calculated['+this_id+']';
        var this_subtotal = OrdenesCompraLib.filterMoney(document.getElementById(id_this_subtotal).value);
        var this_id_rate = 'rate['+this_id+']';
        var rate_value = OrdenesCompraLib.filterMoney(document.getElementById(this_id_rate).value);



        if($(this).attr('id').split('[')[0] !== 'rate' && (document.getElementById(this_id_rate).value === '' || document.getElementById(this_id_rate).value === 0)){

            document.getElementById(id_this_subtotal).disabled = true;
            document.getElementById(this_id_rate).disabled = true;
            document.getElementById(id_icon).style.display = 'block';
            document.getElementById(id_this_calculated).style.display = 'none';
            $.ajax({
                type:'GET',
                url: '/convertInvoiceCurrency/',
                data: {
                    id_order : this_id,
                    invoice_currency : id_currency,
                    invoice_date : invoice_date,
                },
                dataType: 'json',

                success: function(result){

                    document.getElementById(this_id_rate).value = result;
                    result = result.replace(',','.');
                    //si es clp le sacamos los decimales
                    var decimals_total = 2;
                    if(document.getElementById('currency_symbol_'+this_id).value === '$'){
                        result = Math.round(result);
                        decimals_total = 0;
                    }
                    var calculated_total = result*this_subtotal;
                    //Asignacion de valores
                    document.getElementById(id_this_calculated).value = calculated_total.toLocaleString(undefined,
                        {minimunFractionDigits: decimals_total});

                    //Cambios de estado
                    document.getElementById(id_this_subtotal).disabled = false;
                    document.getElementById(this_id_rate).disabled = false;
                    document.getElementById(id_this_calculated).style.display = 'block';
                    document.getElementById(id_icon).style.display = 'none';
                    document.getElementById(id_this_subtotal).focus();
                }
            });

        }else{
            var total = parseFloat(this_subtotal) * parseFloat(rate_value);

            //si es clp le sacamos los decimales
            var decimals_total = 2;
            if(document.getElementById('currency_symbol_'+this_id).value === '$'){
                total = Math.round(total);
                decimals_total = 0;
            }else{
                total = Math.round(total * 100) / 100; //Acortamos a solo dos ceros
                total = addZeroes(''+total); // agregamos .00 de ser necesario
            }

            if(rate_value === "" || document.getElementById(id_this_subtotal).value === ""){
                total = "0";
            }

            document.getElementById(id_this_calculated).value = total;
        }
        var this_class = 'money_'+this_id;
        OrdenesCompraLib.maskMoneyBySymbol(document.getElementById('currency_symbol_'+this_id).value,this_class);
    }

    var displaySelectedPurchaseOrders = function(){

        var len = theBillingOrdersGrid.rows( { selected: true } ).data().length;

        if ($.fn.DataTable.isDataTable("#selectedOrdersGrid")) {
            $('#selectedOrdersGrid').DataTable().clear().destroy();
        }

        theSelectedOrdersGrid = $('#selectedOrdersGrid').DataTable({
            "aoColumns": [
                { "sType": "string", "bSearchable": false, "bSortable": false, "bVisible": true },
                { "sType": "string", "bSearchable": false, "bSortable": false, "bVisible": true, "sClass":"right-column"  },
                { "bSearchable": false, "bSortable": false, "bVisible": true},
                { "bSearchable": false, "bSortable": false, "bVisible": true},
                { "bSearchable": false, "bSortable": false, "bVisible": true},
            ],
            paging:         false,
            "searching": false,
            "bLengthChange": false,
            "bInfo" : false
        });

        addAssignatedPurchaseOrders();

        if(len > 0){
            var ordersSelected = theBillingOrdersGrid.rows( { selected: true } ).data();
            var total_bill = $("#total_bill").text();
            var total_bill_currency = total_bill.split(' ')[1];
            for(var i=0; i<len; i++){
                var obj= ordersSelected[i];
                var currency_oc = obj[5].split(' ')[1];
                var value_rate = "";
                var disabled_rate = "";
                var class_input_currency = 'money_'+obj[1];

                //Si la oc tiene la misma moneda de la factura no se permitira cambiar la tasa de cambio
                if(total_bill_currency === currency_oc){
                    value_rate = 1;
                    disabled_rate = "readonly";
                }
                var sub_total = 0;
                if(currency_oc === "$"){
                    sub_total = obj[5].split(' ')[0]; //sacamos el signo
                    sub_total = sub_total.split(',')[0]; //

                    sub_total = parseInt(sub_total);
                }

                addRowToSelectedOrdersGrid(obj[1],obj[5],value_rate,disabled_rate,class_input_currency);

                OrdenesCompraLib.maskMoneyBySymbol(currency_oc,class_input_currency);
            }
        }

        // $('#total_bill').val($('#total').val());
        $("#total_bill").removeClass('with-error');
        $("#column_rate").width(95);
        $("#pending_to_bill").width(130);
        $("#column_subtotal").width(200);
        $("#column_calculated").width(120);
        $("#mensaje_error_detalle").empty();
        OrdenesCompraLib.maskMoney(id_currency);

        $('.exchange_rate').on('keyup', addEventToCalculateByExchangeRate );
    };

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
                '<td class="right-column"> '+details[i].price+'</td>'+
                '</tr>';
        }
        out += '</table>';
        return out;
    };
    
    function addZeroes(num) {
        var value = Number(num);
        var res = num.split('.');
        if(num.indexOf('.') === -1) {
            value = value.toFixed(2);
            num = value.toString();
        } else if (res[1].length < 3) {
            value = value.toFixed(2);
            num = value.toString();
        }
        return num
    }

    function restrictCharacter(){
        var this_value = String.fromCharCode(event.keyCode);
        var coma = $(this).val().indexOf(",");
        var exclude = coma === -1 ? "[0-9,]":"[0-9]"; //solo aceptar 1 coma
        var exp = new RegExp(exclude);
        var test = exp.test(this_value);
        if(!test){
            event.preventDefault();
            return false;
        }
    }

    $('#activarFiltro').on('click', function() {
        $('#addOC_div').show();
    });

    $('#billingOrdersGrid tbody').on('click','td.details-control', function(){
        var tr = $(this).closest('tr');
        var idx = $.inArray(tr.attr('id'), detailRows );
        var row = theBillingOrdersGrid.row( tr );
        var idOrder = tr.find('td:eq(2)').text();
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

    $('#provider').bind("input", function(e) {

        if(this.value.length >= 3 ) {
            // Call the API search function
            theBillingOrdersGrid.search(this.value).draw();
        }
        // Ensure we clear the search if they backspace far enough
        if(this.value == "") {
            theBillingOrdersGrid.search("").draw();
        }
        return;
    });

    $('.dataTables_filter input').unbind();

    $('.filter').on('keyup', function() {
        if(this.value.length >= 2 ) {
            var searchValue = ",";
            if ($('#monthIni').val() != "") {
                searchValue = $('#monthIni').val() + ',';
            }
            if ($('#monthEnd').val() != "") {
                searchValue = searchValue + $('#monthEnd').val();
            }
            theBillingOrdersGrid.column($(this).data('columnIndex')).search(searchValue).draw();
        }else{
            theBillingOrdersGrid.column($(this).data('columnIndex')).search("").draw();
        }
    });





});

