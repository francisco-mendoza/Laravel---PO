/**
 * CreatedPurchaseOrder JS
 * 2017
 */

var add_product = true;
var proveedores;
var meses;
var ordenesPorMes;


$(document).ready(function(){

    /** -------------------- */
    /**  Submit Orden Compra */
    /** -------------------- */
    $('#f_orden').on('submitted', function() {


        var promise = new Promise((resolve, reject) => {

                // Serialize the data in the form
                var serializedData = $('#f_orden').serialize();

                $.ajax({
                    url:   '/validatePurchaseOrders',
                    type:  'post',
                    data: serializedData,
                    beforeSend: function() {
                        $('#loading').modal('toggle');
                    },
                    success:  function (response) {
                        ordenesPorMes = response;
                        $('#loading').modal('hide');
                        $('#ordersByMonth').val(JSON.stringify(response));
                        resolve(true);
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.log(textStatus, errorThrown);

                        let reason = new Error(errorThrown);
                        reject(reason);

                        // Throwing an error also rejects the promise.
                        throw reason;
                    },
                    fail: function(error){
                        let reason = new Error(error);
                        reject(reason);

                        // Throwing an error also rejects the promise.
                        throw reason;
                    }
                });

            });


            promise.then(value => {

                let json = JSON.stringify(ordenesPorMes);
                let jsonByMonth = JSON.parse(json);

                let totalsByMonth = 0;
                let totalsIVAByMonth = 0;
                let necesitaConfirmacion = true;
                let manyDetails = 0;
                let message = "<span>Según lo que especificaste se crearán las siguientes ordenes de compra:</span><br /><br />";

                message = message + "<span>Proveedor: " + $('#name_provider').val() +"</span><br /><br />";
                message = message + "<div style='overflow-y: auto; max-height: 267px;'><table class='table table-striped table-framed table-condensed' style='width:100%'><thead> <tr> <th style='width:33.3%;'> Mes </th> <th style='width:33.3%;'> Total </th> <th style='width:33.3%;'>Total con Iva</th></tr></thead><tbody>";
                for(var j = 1; j<=12; j++){
                    if( jsonByMonth[j].length > 0){ //Si el mes tiene detalle
                        manyDetails = manyDetails + 1;
                        let len = jsonByMonth[j].length;
                        for(var k = 0; k<len ; k++){
                            totalsByMonth = totalsByMonth + parseFloat(jsonByMonth[j][k].price);
                            if(jsonByMonth[j][k].hasOwnProperty('price_iva')){
                                totalsIVAByMonth = totalsIVAByMonth + parseFloat(jsonByMonth[j][k].price_iva);
                            }else{
                                totalsIVAByMonth = totalsIVAByMonth + parseFloat(jsonByMonth[j][k].price);
                            }
                        }

                        // message = message + "<tr><td style='text-align:left;'>"+ meses[j]+ "</td><td class='right-column'>"+ totalsByMonth.toFixed(2)+"</td><td class='right-column'>"+totalsIVAByMonth.toFixed(2)+"</td></tr>";
                        message = message + "<tr><td style='text-align:left;'>"+ meses[j]+ "</td><td class='right-column'>"+ $.number(totalsByMonth, 2, ',', '.') +"</td><td class='right-column'>"+$.number(totalsIVAByMonth, 2, ',', '.')+"</td></tr>";
                        totalsByMonth = 0;
                        totalsIVAByMonth = 0;

                        //Validar si la orden de compra está dividida en más de un mes
                        //if(manyDetails == 2){  necesitaConfirmacion = true; }
                    }
                }
                message = message + "<tr><td style='font-weight: 500 '> Total</td><td class='right-column' style='font-weight: 500 '>"+ $('#total_sin_iva').val()+"</td><td style='font-weight: 500 ' class='right-column'>"+ $('#total').val()+"</td></tr>";
                message = message + "</tbody> </table></div>";

                if(necesitaConfirmacion){

                    swal({
                            title: "Está seguro?",
                            text: message,
                            type: "warning",
                            html: true,
                            showCancelButton: true,
                            confirmButtonColor: "#4CAF50",
                            confirmButtonText: "Si, generar orden!",
                            cancelButtonText: "No, necesito volver!",
                            closeOnConfirm: false,
                            closeOnCancel: false
                        },
                        function(isConfirm){
                            if (isConfirm) {
                                $('.confirm').prop('disabled',true);

                                $("#f_orden").submit();
                            } else {
                                swal("Cancelado", "Edita e intenta nuevamente!", "error");
                            }
                        });

                }

            });

            promise.catch(reason => {
                // Something went wrong,
                // promise has been rejected with "reason"
                $('#loading').modal('hide');
            });

    });


    $('[data-toggle="tooltip"]').tooltip()

    /** ---------------- */
    /**  Click Guardar   */
    /** ---------------- */
    $("#guardar").on("click",function(e){

        OrdenesCompraLib.maskMoney($("#currency").val());

        $("#f_orden").validator({
            custom: {
                'containsProvider': function () {
                    if($.inArray($('#name_provider').val(), proveedores) == -1){
                        return "Debe seleccionar un proveedor de la lista";
                    }
                },
                'selectMethod' : function(){
                    if($('#payment_method').val() === null ||  $('#payment_method').val() === ""){
                        return "Debe seleccionar un método de pago";
                    }
                },
                'selectCondition' : function(){
                    if($('#payment_condition').val() === null ||  $('#payment_condition').val() === ""){
                        return "Debe seleccionar una condición de pago";
                    }
                },
                'selectCurrency' : function(){
                    if($('#currency').val() === null ||  $('#currency').val() === ""){
                        return "Debe seleccionar una moneda";
                    }
                }
            }
        });


        if ((!$("#f_orden").data("bs.validator").validate().hasErrors()) ) {

            e.preventDefault();

            //Validar el detalle
            let detailOK = validateDetail();
            let moneyOK = validateMoney();

            if(!detailOK || !moneyOK){
                $("#mensaje_error_detalle").html('<p style="color:#D84315">Complete los campos en rojo:</p>');
            }else{
                $("#mensaje_error_detalle").empty();
            }

            if(detailOK && moneyOK){
                    // Here go the trick! Fire a custom event to the form
                    $("#f_orden").trigger('submitted');

            }

        } else  {
            console.log('Form still not valid');
        }

        //Validar el detalle
        // let detailOK = validateDetail();
        //
        // if(!detailOK){
        //     e.preventDefault();
        // }else{
        //     if ((!$("#f_orden").data("bs.validator").validate().hasErrors()) ) {
        //
        //         e.preventDefault();
        //         // Here go the trick! Fire a custom event to the form
        //         $("#f_orden").trigger('submitted');
        //     } else  {
        //         console.log('Form still not valid');
        //     }
        // }
    });

    var initialize = function(){

        $('.panel-body .paso-2').attr('disabled','disabled');
        $('.panel-body .paso-2').addClass('opacity');

        $('.panel-body .paso-3').attr('disabled','disabled');
        $('.panel-body .paso-3').addClass('opacity');

        $('.panel-body .paso-4').attr('disabled','disabled');
        $('.panel-body .paso-4').addClass('opacity');

        $('.panel-body .paso-5').attr('disabled','disabled');
        $('.panel-body .paso-5').addClass('opacity');
    };

    $('body').addClass('sidebar-xs');
    initialize();

    /** ------------------------- */
    /**  Condicion de pago change */
    /** ------------------------- */
    $('#payment_condition').on('change',function(){
       //Activar paso 3
        $('.panel-body .paso-3').attr('disabled',false);
        $('.panel-body .paso-3').removeClass('opacity');
    });

    /** ------------------------ */
    /**  Metodos de pago change  */
    /** ------------------------ */
    $('#payment_method').on('change',function(){
        //Activar paso 4
        $('.panel-body .paso-4').attr('disabled',false);
        $('.panel-body .paso-4').removeClass('opacity');
    });

    /** ---------------- */
    /**  Change Currency */
    /** ---------------- */
    $('#currency').on('change',function () {
        //Activar paso 5
        $('.panel-body .paso-5').attr('disabled',false);
        $('.panel-body .paso-5').removeClass('opacity');

    });


    /** ------------------- */
    /**  Change Tipo Boleta */
    /** ------------------- */
    $('input[name=tipo_boleta]').on('change',function(){
       let val_boleta = parseInt($(this).val());
        $('.money,#total,#total_sin_iva').val(0);
        $('.cantidad').val(1);
        OrdenesCompraLib.maskMoney($("#currency").val());
       switch(val_boleta){
           case Factura:
               $('#importe_primero').text('Importe Unitario por mes s/IVA');
               $('#importe_segundo').text('Importe Unitario por mes c/IVA');
               $('#tipo_impuesto').text('IVA');
               $('#lbl_total_sin_iva').text('Total Sin Iva');
               $('#lbl_total_con_iva').text('Total Con Iva');
               break;
           case Honorarios:
               $('#importe_primero').text('Importe Unitario por mes s/Honorario');
               $('#importe_segundo').text('Importe Unitario por mes c/Honorario');
               $('#tipo_impuesto').text('Retención Honorarios');
               $('#lbl_total_sin_iva').text('Total Sin Honorarios');
               $('#lbl_total_con_iva').text('Total Con Honorarios');
               break;
       }
    });


    /** ----------------------- */
    /**  Trae a los proveedores */
    /** ----------------------- */
    var getProviderInfo = function($name){

        $.ajax({
            url:   '/getProviderInfo/'+$name,
            type:  'get',
            success:  function (response) {
                //debugger;
                $('#address').val(response.address);
                $('#rut').val(response.rut);
                $('#phone').val(response.phone);


                initialize();

                //Activar el paso 2
                $('.panel-body .paso-2').attr('disabled',false);
                $('.panel-body .paso-2').removeClass('opacity');

                //Vemos si tienen condiciones y metodos de pagos para autocompletarlos
                if(response.payment_conditions!=null){
                    $('#payment_condition').val(response.payment_conditions).change();
                    $('#payment_condition option:not(:selected)').attr('disabled',true);
                }else{
                    //Si no tiene, reseteamos el select
                    $('#payment_condition option').prop('selected', function() {
                        $('#payment_condition option:not(:selected)').attr('disabled',false);
                        return this.defaultSelected;
                    });
                }
                if(response.payment_method !=null){
                    $('#payment_method').val(response.payment_method).change();
                    $('#payment_method option:not(:selected)').attr('disabled',true);

                    $('#currency option').prop('selected', function() {
                        $('#currency option:not(:selected)').attr('disabled',false);
                        return this.defaultSelected;
                    });
                }else{
                    $('#payment_method option').prop('selected', function() {
                        $('#payment_method option:not(:selected)').attr('disabled',false);
                        return this.defaultSelected;
                    });
                }
            }
        });
    };

    var substringMatcher = function(strs) {
        return function findMatches(q, cb) {
            var matches, substringRegex;

            // an array that will be populated with substring matches
            matches = [];

            q = q.replace(/([.?*+^$[\]\\(){}|-])/g, "\\$1");

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

    /** --------------------------------------- */
    /**    Proveedores / Contrato Typeahead     */
    /** --------------------------------------- */
    $.ajax({
        url:   '/getProviders',
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

                var value = $('#name_provider').val().split(/[()]+/).filter(function(e) { return e; });

                if($.inArray($('#name_provider').val(), proveedores) !== -1){
                    //Mostramos solo el proveedor
                    var name_provider = value[0].split(' - ');
                    getProviderInfo(name_provider[0]);
                }
            });

        }
    });


    //Check focus anterior
    var _prevFocus;
    let body_product_oc = $("#body_product_oc");
    body_product_oc.on({
        focus:function () {
            _prevFocus = $(this).attr('id');
            if($(this).val() == 0){
                $(this).val('');
            }
        },
        focusout:function () {
            if($(this).val() == ""){
                $(this).val(0);
            }
        }
    },'.money');

    body_product_oc.on({
        'keyup change':function () {
            calcularTotal();
            OrdenesCompraLib.maskMoney($("#currency").val());
        },
        focusout:function(){
            if($(this).val() == ""){
                $(this).val(1);
                OrdenesCompraLib.maskMoney($("#currency").val());
            }
        }
    },'.cantidad');


    /** ---------------- */
    /**    Check Iva     */
    /** ---------------- */
    body_product_oc.on('click','.checkiva',function(e){

        if($("#currency").val() != 2){
            OrdenesCompraLib.maskMoney($("#currency").val());
            return false;
        }

        //debugger;
        let _count = $(this).attr('data-action');
        let id_check = $(this).attr('id');
        let priceWithIva_actual = $('#priceWithIva_'+_count);
        if($(this).hasClass('checked')){
            $(this).removeClass('checked');
            $('#span_'+_count).removeClass('checked');
            priceWithIva_actual.attr('disabled','disabled');
            priceWithIva_actual.val('');
        }else {
            $('#'+id_check).addClass('checked');
            $('#span_'+_count).addClass('checked');

            //checkIva(_count,_prevFocus);

            priceWithIva_actual.removeAttr('disabled');

            let valWithoutIva = OrdenesCompraLib.filterMoney($('#priceWithoutIva_'+_count).val());
            let valor_con_iva = 0;
            if(valWithoutIva>0){
                let tipo_boleta = parseInt($('input[name=tipo_boleta]:checked').val());
                if(tipo_boleta == Factura){
                    valor_con_iva = OrdenesCompraLib.calcularIva($('#priceWithoutIva_'+_count).val(),'agregar');
                }else{
                    valor_con_iva = OrdenesCompraLib.calcularHonorario($('#priceWithoutIva_'+_count).val(),'agregar');
                }

                valor_con_iva = OrdenesCompraLib.desFilterMoney(valor_con_iva);
                $('#priceWithIva_'+_count).val(valor_con_iva)
            }
        }

        calcularTotal();
        OrdenesCompraLib.maskMoney($("#currency").val());
    });


    /** ---------------- */
    /**    Check Meses   */
    /** ---------------- */
    body_product_oc.on('click','.checkMonth',function(){

        let _count = $(this).attr('data-action');
        let id_check = $(this).attr('id');

        if($(this).hasClass('checked')){
            $(this).removeClass('checked');
            $('#span_month_'+_count).removeClass('checked');
            $('#month_end_'+_count).addClass('opacity');
            $('#month_end_'+_count).attr('disabled',true);
            $('#cant_'+_count).attr('readonly',false);
        }else {
            if($('#month_ini_'+_count).val() == '12'){
                return false;
            }
            $('#'+id_check).addClass('checked');
            $('#span_month_'+_count).addClass('checked');
            $('#month_end_'+_count).removeClass('opacity');
            $('#month_end_'+_count).attr('disabled',false);
            $('#cant_'+_count).val(1).attr('readonly',true);

            if($('#month_ini_'+_count).val() == '1'){
                $('#month_ini_'+_count+' option[value="1"]').attr('selected',true);
                $('#month_end_'+_count+' option[value="1"]').attr('disabled',true);
                $('#month_end_'+_count+' option[value="2"]').attr('selected',true);
            }

        }
        calcularTotal();
        OrdenesCompraLib.maskMoney($("#currency").val());
    });

    //Si intenta modificar total o subtotal a mano, calcula el total denuevo
    $('#total,#total_sin_iva').on('keyup',function(){
        calcularTotal();
    });

    body_product_oc.on('change','.comboMonth',function(){
        let this_id = $(this).attr('id');
        let this_id_split = this_id.split('_');
        let month_type = this_id_split[0]+this_id_split[1];
        let this_count = this_id_split[2];
        let primer_disponible = true;

        if(month_type == 'monthini' && $(this).val()!=12){
            //Recorremos el select end
            $('#month_end_'+this_count+'> option').each(function(){
                //Opcion actual
                let option_select = parseInt($(this).val());
                //Mes inicial
                let mount_ini_count = parseInt($('#month_ini_'+this_count).val());
                //Si la opcion actual es menor o igual al Mes inicial seleccionado
                if(option_select <= mount_ini_count){
                    $(this).attr('disabled',true);
                }else{
                    //Si no, lo habilitamos
                    $(this).attr('disabled',false);
                    //Esto es para dejar la primera opcion disponible seleccionada
                    if(primer_disponible){
                        $(this).attr('selected',true);
                        primer_disponible = false;
                    }else{
                        //Sacamos los selected anteriores
                        $(this).attr('selected',false);
                    }
                }
            });
        }else if(month_type == 'monthini' && $(this).val()==12){
            // $('#month_'+this_count).removeClass('checked');
            // $('#span_month_'+this_count).removeClass('checked');
            // $('#month_end_'+this_count).addClass('opacity').attr('disabled',true);
            // $('#cant_'+this_count).attr('readonly',false);
            $('#month_'+this_count).trigger('click');
        }
        calcularTotal();
        OrdenesCompraLib.maskMoney($("#currency").val());
    });

    body_product_oc.on('keyup','.money',function(){
        let id_select = $(this).attr('id');
        let split_select = id_select.split('_');
        let actualPriceWithIva = $('#priceWithIva_'+split_select[1]);
        let actualPriceWithOutIva = $('#priceWithoutIva_'+split_select[1]);
        let tipo_boleta = parseInt($('input[name=tipo_boleta]:checked').val());
        if(!actualPriceWithIva.is('[disabled=disabled]')){
            if(split_select[0] == 'priceWithoutIva'){
                let monto_con_iva;
                if(tipo_boleta == Factura){
                    monto_con_iva = OrdenesCompraLib.calcularIva($(this).val(),'agregar');
                }else{
                    monto_con_iva = OrdenesCompraLib.calcularHonorario($(this).val(),'agregar');
                }

                monto_con_iva = OrdenesCompraLib.desFilterMoney(monto_con_iva);
                actualPriceWithIva.val(monto_con_iva);
            }else{
                let monto_sin_iva = (tipo_boleta == Factura) ? OrdenesCompraLib.calcularIva($(this).val(),'quitar'):OrdenesCompraLib.calcularHonorario($(this).val(),'quitar');
                monto_sin_iva = OrdenesCompraLib.desFilterMoney(monto_sin_iva);
                actualPriceWithOutIva.val(monto_sin_iva);

            }
        }
        calcularTotal();
        OrdenesCompraLib.maskMoney($("#currency").val());
    });

    localStorage.setItem("countProduct", 0);

    $('#add_product').click(function(){
        let count = parseInt(localStorage.getItem("countProduct"))+1;

        let html = `
        <tr id="product_item_${count}" class="items">
        <td><input type="number" name="cant_${count}" id="cant_${count}" class="form-control cantidad paso-5" min="1" value="1"  ></td>
        <td><input type="text" name="desc_${count}" id="desc_${count}" class="form-control paso-5"  > </td>
        <td>
            <div class="form-inline col-sm-12 full-width-padding" >
                <div class="col-sm-5 none-padding"  >
                    <select name="month_ini_${count}" id="month_ini_${count}" class="form-control full-width paso-5 comboMonth"  ></select>
                </div>
                <div class="col-sm-2 none-padding " >
                    <div class="checker border-info-600 text-info-800 center-check ">
                        <div class="checker " id="uniform-month_${count}">
                            <span class="" id="span_month_${count}">
                                <input type="checkbox" data-action="${count}" name="month_${count}" id="month_${count}" class="form-control styled checkMonth paso-5" >
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-sm-5 none-padding "   >
                    <select name="month_end_${count}" id="month_end_${count}" class="form-control col-sm-6  full-width comboMonth opacity"  disabled="disabled" ></select>
                </div>
            </div>
        </td>
        <td><input type="text" name="priceWithoutIva_${count}" id="priceWithoutIva_${count}" class="form-control money paso-5"  value="0"></td>
        <td>
            <div class="checker border-info-600 text-info-800 ">
                <div class="checker " id="uniform-iva_${count}">
                    <span class="" id="span_${count}">
                        <input type="checkbox" data-action="${count}" name="iva_${count}" id="iva_${count}" class="form-control styled checkiva paso-5" >
                    </span>
                </div>
            </div>
        </td>
        <td><input type="text" name="priceWithIva_${count}" id="priceWithIva_${count}" class="form-control money" disabled="disabled" value="0"></td>
        <td><a class="delete_item paso-5" data-action="${count}"><i class="fa fa-trash-o fa-lg " aria-hidden="true"></i></a></td>
        </tr>`;

        $('#body_product_oc').append(html);
        localStorage.setItem("countProduct", count);
        $('#count_detail').val(count);


        $('.delete_item').click(function(e){
            if($('#currency').val()==null || $('#currency').val()==""){
                e.preventDefault();
            }else{
                let count = $(this).attr('data-action');
                $('#product_item_'+count+'').remove();
                calcularTotal();
            }
            OrdenesCompraLib.maskMoney($("#currency").val());
        });
        setMounths(count);
        OrdenesCompraLib.maskMoney($("#currency").val());
    });

    //Formato Monedas
    $('#currency').change(function() {
        $('.money,#total,#total_sin_iva').val(0);
        $('.cantidad').val(1);
        OrdenesCompraLib.maskMoney($("#currency").val());
    });




    if(add_product){
        $('#add_product').trigger('click');
        initialize();
        add_product = false;
    }


    //Primer item
    $.ajax({
        url:   '/getMonths',
        type:  'get',
        success:  function (response) {
            meses = response;
            var selIni = document.getElementById('month_ini_1');
            var selEnd = document.getElementById('month_end_1');
            for (var key in meses) {
                var opt = document.createElement('option');
                var optEnd = document.createElement('option');
                opt.innerHTML = meses[key];
                optEnd.innerHTML = meses[key];
                opt.value = key;
                optEnd.value = key;
                selIni.appendChild(opt);
                selEnd.appendChild(optEnd);
            }


        }


    });


});


/**===================================*/
/**            FUNCIONES              */
/**===================================*/


/** ---------------- */
/**  Validar Detalle */
/** ---------------- */
function validateDetail(){
    let isValid = true;

    let descs = $('input[name^=desc]');

    for(var i = 0; i<descs.length ; i++){
        if($('#'+descs[i].id).val() == ""){
            isValid = false;
            $('#'+descs[i].id).addClass('with-error');
            $('#'+descs[i].id).keyup(function(){
                $(this).removeClass('with-error');
                $("#mensaje_error_detalle").empty();
            });
        }
    }

    return isValid;
}

/** ------------------------ */
/**  Validar Montos Detalle  */
/** ------------------------ */
function validateMoney(){
    let moneyFalse = true;
    $('.money').each(function(){
        //debugger;
        let this_val = OrdenesCompraLib.filterMoney($(this).val());
        let this_id = $(this).attr('id');
        let this_id_split = this_id.split('_');
        if(this_id_split[0] == 'priceWithoutIva' && (this_val == 0 || this_val == '' || isNaN(this_val))){
            moneyFalse = false;
            $(this).addClass('with-error');
            $(this).keyup(function(){
                $(this).removeClass('with-error');
                $("#mensaje_error_detalle").empty();
            });
        }
    });

    return moneyFalse;
}


function setMounths(count){
    var selIni = document.getElementById('month_ini_'+count);
    var selEnd = document.getElementById('month_end_'+count);
    for (var key in meses) {
        var opt = document.createElement('option');
        var optEnd = document.createElement('option');
        opt.innerHTML = meses[key];
        optEnd.innerHTML = meses[key];
        opt.value = key;
        optEnd.value = key;
        selIni.appendChild(opt);
        selEnd.appendChild(optEnd);
    }
}

function calculoEntreMeses(count){
    return parseInt($('#month_end_'+count).val()) - parseInt($('#month_ini_'+count).val()) + 1 ;
}


function calcularTotal(){
    var Totales = calcularTotalOC();
    let total = Totales.total;
    let total_sin_iva = Totales.total_sin_iva;


    $('#guardar').attr('disabled',true);
    validaCupoArea(total_sin_iva,$('#currency').val(), $('#name_provider').val(),$('#purchase_order').val());
    //Se convierte en el formato que recibe maskMoney
    total_sin_iva = OrdenesCompraLib.desFilterMoney(total_sin_iva);
    total = OrdenesCompraLib.desFilterMoney(total);

    $('#total_sin_iva').val(total_sin_iva);
    $('#total').val(total);
}



function validaCupoArea(total,moneda, proveedor, order){
    let idBtnGuardar = '#guardar';
    let provider_contract = proveedor.split('(');
    $.ajax({
        url:   '/validateAreaBudget',
        type:  'get',
        data:{
            cantidad:total,
            moneda_origen:moneda,
            moneda_destino:2,
            id_area:id_area,
            proveedor:provider_contract[0],
            order:order,
            action:'create',
        },
        success:  function (response) {
            if(response == "false")
            {
                toastr.options = {
                    "closeButton": true,
                    "progressBar": true,
                    "preventDuplicates": true,
                    "showDuration": "2000",
                    "hideDuration": "2000",
                    "timeOut": "9000",
                    "extendedTimeOut": "1000",
                };
                toastr["error"]("Has excedido el monto maximo de tu área.", "Monto excedido");
                $(idBtnGuardar).attr('disabled',true);

            }
            else
            {
                toastr.clear();
                $(idBtnGuardar).attr('disabled',false);
                $('.confirm').attr('disabled',false);
            }
        }
    });


}



