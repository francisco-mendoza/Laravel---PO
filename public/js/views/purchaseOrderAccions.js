/**
 * Created by francisco on 03-05-17.
 */


//Id's Currency
const PesoChileno   = 2;
const Dolar         = 3;
const Euro          = 4;
const UnidadFomento = 5;
const CoronaNoruega = 6;
const CoronaSueca   = 7;

//Tipo Boletas
const Factura = 1;
const Honorarios = 2;

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

    if(!isValid){
        $("#mensaje_error_detalle").html('<p style="color:#D84315">Complete los campos en rojo:</p>');
    }else{
        $("#mensaje_error_detalle").empty();
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

    if(!moneyFalse){
        $("#mensaje_error_detalle").html('<p style="color:#D84315">Complete los campos en rojo:</p>');
    }else{
        $("#mensaje_error_detalle").empty();
    }
    return moneyFalse;
}


function calculoEntreMeses(count){
    return parseInt($('#month_end_'+count).val()) - parseInt($('#month_ini_'+count).val()) + 1 ;
}


function calcularTotalOC(){
    let total = 0;
    let total_sin_iva = 0;
    $('.money').each(function(){
        //Calculo por cada textbox
        let input_actual = $(this).attr('id');
        let input_actual_split = input_actual.split('_');

        let this_valor = ($(this).val() == '') ? 0 : $(this).val();

        let cantidad = $('#cant_'+input_actual_split[1]).val() == ''? 0:$('#cant_'+input_actual_split[1]).val();
        this_valor = OrdenesCompraLib.filterMoney(this_valor);

        if(input_actual_split[0] == 'priceWithoutIva'){
            //Si esta seleccionado más de un mes en el item, lo multiplicamos por la cantidad
            if($('#month_'+input_actual_split[1]).hasClass('checked')){
                let calculoMeses = calculoEntreMeses(input_actual_split[1]);
                this_valor = this_valor * calculoMeses;
            }
            total_sin_iva = parseFloat(total_sin_iva) + parseFloat(this_valor)*parseFloat(cantidad);

        }else if(input_actual_split[0] == 'priceWithIva'){
            //Estoy en un priceWithIva
            if(this_valor > 0){
                //Si esta seleccionado más de un mes en el item, lo multiplicamos por la cantidad
                if($('#month_'+input_actual_split[1]).hasClass('checked')){
                    let calculoMeses = calculoEntreMeses(input_actual_split[1]);
                    this_valor = this_valor * calculoMeses;
                }
                total = total + parseFloat(this_valor)*parseFloat(cantidad);
            }else{
                let priceWithoutIva = $('#priceWithoutIva_'+input_actual_split[1]).val();
                priceWithoutIva = OrdenesCompraLib.filterMoney(priceWithoutIva);
                let valor_priceWithoutIva = priceWithoutIva == ''?0 : priceWithoutIva;
                //Si esta seleccionado más de un mes en el item, lo multiplicamos por la cantidad
                if($('#month_'+input_actual_split[1]).hasClass('checked')){
                    let calculoMeses = calculoEntreMeses(input_actual_split[1]);
                    valor_priceWithoutIva = parseFloat(valor_priceWithoutIva) * calculoMeses;
                }
                total = total + parseFloat(valor_priceWithoutIva)*parseFloat(cantidad);
            }

        }


    });

    return {total:total,total_sin_iva:total_sin_iva};
}
