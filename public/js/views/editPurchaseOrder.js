/**
 * Created by francisco on 03-05-17.
 */

var total_oc_;

$(document).ready(function(){


    let body_product_oc = $("#body_product_oc");
    let path = window.location.pathname;
    let orden_compra_edit = path.split('/')[2];

    if(old_folio_number != ""){
        $('#descripcion_detalles').tooltip().attr('data-original-title','OC antigua, cada descripción debe iniciar con el N° cuenta ');
    }


    /** -------------------- */
    /**  Submit Orden Compra */
    /** -------------------- */
    $('#guardar').on('click', function() {

        var tableRow = $("#cuentasContrato tr td").filter(function() {
            return $(this).text() == "No hay datos disponibles";
        }).parent('tr');

        $("#items").val("");
        if(tableRow.length == 0){
            var ini = 1;
            var campo = 1;
            var item = 1;

            $("#body_product_oc tr").find('input').each(function(){

                $this = $(this);

                var content = "";

                if(campo == 1){
                    content = (item != 1 ? ',' : '[') + '{ "cantidad":"' + $this.val() ;
                }else if(campo  == 2){
                    content= '", "description":"' + $this.val();
                }else if(campo  == 3){
                    content= '", "valor_sin_iva":"' + $this.val();
                }else if(campo  == 4){
                    content= '", "has_iva":"' + $this.hasClass('checked');
                }else if(campo == 5){
                    content= '", "valor_con_iva":"' + $this.val()+ '" }';
                }

                $("#items").val($("#items").val() + content);

                if(campo == 5){
                    item = item+1;
                    campo = 1;
                }else{
                    campo = campo + 1;
                }

                ini = ini +1;

            });
            var final =  $("#items").val() + ']';
            $("#items").val(JSON.stringify(JSON.parse(final)));
        }



        var promise = new Promise((resolve, reject) => {

            // Serialize the data in the form
            var serializedData = $('#f_orden').serialize();
            resolve(true);
            $('#loading').modal('toggle');
            // $.ajax({
            //     url:   '/validatePurchaseOrders',
            //     type:  'post',
            //     data: serializedData,
            //     beforeSend: function() {
            //         $('#loading').modal('toggle');
            //     },
            //     success:  function (response) {
            //         ordenesPorMes = response;
            //         $('#loading').modal('hide');
            //         $('#ordersByMonth').val(JSON.stringify(response));
            //         resolve(true);
            //     },
            //     error: function(jqXHR, textStatus, errorThrown) {
            //         console.log(textStatus, errorThrown);
            //
            //         let reason = new Error(errorThrown);
            //         reject(reason);
            //
            //         // Throwing an error also rejects the promise.
            //         throw reason;
            //     },
            //     fail: function(error){
            //         let reason = new Error(error);
            //         reject(reason);
            //
            //         // Throwing an error also rejects the promise.
            //         throw reason;
            //     }
            // });

        });


        promise.then(value => {

            // let json = JSON.stringify(ordenesPorMes);
            // let jsonByMonth = JSON.parse(json);
            $('#loading').modal('hide');

            let totalsByMonth = 0;
            let totalsIVAByMonth = 0;
            let necesitaConfirmacion = true;
            let manyDetails = 0;
            let message = "<span>Según lo que modificaste este será el nuevo total para esta Orden de Compra:</span><br /><br />";

            message = message + "<span>Proveedor: " + $('#name_provider').val() +"</span><br /><br />";
            message = message + "<div style='overflow-y: auto; max-height: 267px;'><table class='table table-striped table-framed table-condensed' style='width:100%'><thead> <tr> <th style='width:33.3%;'> Mes </th> <th style='width:33.3%;'> Total </th> <th style='width:33.3%;'>Total con Iva</th></tr></thead><tbody>";

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
                        confirmButtonText: "Si, editar orden!",
                        cancelButtonText: "No, necesito volver!",
                        closeOnConfirm: false,
                        closeOnCancel: false
                    },
                    function(isConfirm){
                        if (isConfirm) {
                            $('.confirm').prop('disabled',true);

                            $("#f_orden").submit();
                            //modificarOrden(orden_compra_edit,filterMoney($('#total_sin_iva').val()),filterMoney($('#total').val()),$('#items').val());
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

    $('body').addClass('sidebar-xs');
    OrdenesCompraLib.maskMoney($("#currency").val());
    // maskMoney();
    calcularTotal();

    for(var a = 1 ; a <= parseInt(actual_count) ; a++){
        // debugger;
        $("#checkiva_row_"+a+" span").attr('id',"2222");
    }

    $('.delete_item').click(function(e){
        if($('#currency').val()==null || $('#currency').val()==""){
            e.preventDefault();
        }else{
            let count = $(this).attr('data-action');
            $('#product_item_'+count+'').remove();
            calcularTotal();
        }
        OrdenesCompraLib.maskMoney($("#currency").val());
        // maskMoney();
    });

    //Check focus anterior
    var _prevFocus;

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

    /** Change Cantidad */
    body_product_oc.on({
        'keyup change':function () {
            calcularTotal();
            OrdenesCompraLib.maskMoney($("#currency").val());
            // maskMoney();
        },
        focusout:function(){
            if($(this).val() == ""){
                $(this).val(1);
                // maskMoney();
                OrdenesCompraLib.maskMoney($("#currency").val());
            }
        }
    },'.cantidad');


    /** ---------------- */
    /**    Check Iva     */
    /** ---------------- */
    body_product_oc.on('click','.checkiva',function(e){
         //debugger;

        if($("#currency").val() != 2){
            OrdenesCompraLib.maskMoney($("#currency").val());
            //maskMoney();
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

            // let valWithoutIva = filterMoney($('#priceWithoutIva_'+_count).val());
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
        // maskMoney();
    });


    //Si intenta modificar total o subtotal a mano, calcula el total denuevo
    $('#total,#total_sin_iva').on('keyup',function(){
        calcularTotal();
    });

    body_product_oc.on('keyup','.money',function(){

        let id_select = $(this).attr('id');
        let split_select = id_select.split('_');
        let actualPriceWithIva = $('#priceWithIva_'+split_select[1]);
        let actualPriceWithOutIva = $('#priceWithoutIva_'+split_select[1]);
        let tipo_boleta = parseInt($('input[name=tipo_boleta]:checked').val());
        //Verifico que el valor con iva actual no este desabilitado
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
        // maskMoney();
    });


    localStorage.setItem("countProduct", actual_count);
    $('#add_product').click(function(){
        let count = parseInt(localStorage.getItem("countProduct"))+1;

        let html = `
        <tr id="product_item_${count}" class="items">
        <td><input type="number" name="cant_${count}" id="cant_${count}" class="form-control cantidad paso-5" min="1" value="1"  ></td>
        <td><input type="text" name="desc_${count}" id="desc_${count}" class="form-control paso-5"  > </td>
       
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
            // maskMoney();
        });
        // maskMoney();
        OrdenesCompraLib.maskMoney($("#currency").val());
    });




});

function calcularTotal(){

    var Totales = calcularTotalOC();
    let total = Totales.total;
    let total_sin_iva = Totales.total_sin_iva;

    //$('#guardar').attr('disabled',true);
    validaCupoArea(total_sin_iva,$('#currency').val(), $('#name_provider').val(),$('#purchase_order').val(),$('#items').val(),$('#first_item_description').val());
    //Se convierte en el formato que recibe maskMoney
    total_sin_iva = OrdenesCompraLib.desFilterMoney(total_sin_iva);
    total = OrdenesCompraLib.desFilterMoney(total);

    $('#total_sin_iva').val(total_sin_iva);
    $('#total').val(total);
}

function validaCupoArea(total,moneda, proveedor, order,items,first_item_description){
    let idBtnGuardar = '#guardar';
    let provider_contract = proveedor.split('(');
    //debugger;
    total_price = parseFloat(total_price);
    total = total - total_price;
    $.ajax({
        url:   '/validateAreaBudget',
        type:  'get',
        data:{
            cantidad:total,
            moneda_origen:moneda,
            moneda_destino: 2,
            id_area:id_area,
            proveedor:provider_contract[0],
            order:order,
            action:'edit',
            items:items,
            first_item_description: first_item_description
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