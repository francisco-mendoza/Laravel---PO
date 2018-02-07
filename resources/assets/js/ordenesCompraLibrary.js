(function(window){
    //I recommend this
    'use strict';
    function define_library(){
        var OrdenesCompraLib = {};

        OrdenesCompraLib.preventInput = function(event){
            if (event.which == 40 || event.which == 41 || event.which == 45) {
                event.preventDefault();
                return false;
            }
        };


        OrdenesCompraLib.replaceCharacters = function(name){
            setTimeout(function()
            {
                var n = '#'+name;
                var s= $(n).val() ;
                s = s.replace('-', '');
                s = s.replace(')', '');
                s = s.replace('(', '');
                $(n).val(s);
            });
        };

        OrdenesCompraLib.confirmDelete = function(url,id,tok,theGrid){
            swal({
                    title: "Está seguro?",
                    text: "No se podrá recuperar este registro!",
                    type: "warning",
                    showCancelButton: true,
                    cancelButtonText: "Cerrar",
                    confirmButtonColor: "#4CAF50",
                    confirmButtonText: "Eliminar!",
                    closeOnConfirm: false,
                },
                function(isConfirm){
                    if (isConfirm) {
                        $.ajax({
                            url: url,
                            type: 'DELETE',
                            data: {
                                "id": id,
                                "_method": 'DELETE',
                                "_token": tok,
                            },
                            success: function () {
                                theGrid.ajax.reload();
                                swal("Eliminado!", "El registro ha sido eliminado.", "success");
                            },
                            error: function (thrownError) {
                                var permisos = "";
                                if (thrownError && 'responseJSON' in thrownError) {
                                    permisos = thrownError.responseJSON['message'];
                                }
                                var mensaje = "El registro NO ha sido eliminado. " + ( permisos !== null ? permisos : "" );
                                swal("Error!", mensaje , "error");
                            }
                        });
                    }
                });
        };

        OrdenesCompraLib.maskMoney = function(id_currency){
            let selected_money = parseFloat(id_currency);
            let class_txt_money = ".money";
            $(class_txt_money).maskMoney('destroy');
            switch (selected_money){
                case PesoChileno:
                    $(class_txt_money).maskMoney({prefix:'$ ', allowNegative: true, thousands:'.', decimal:',', precision: 0});
                    break;
                case Dolar:
                    $(class_txt_money).maskMoney({prefix:'US$ ', allowNegative: true, thousands:'.', decimal:',', precision: 2});
                    break;
                case Euro:
                    $(class_txt_money).maskMoney({suffix:'€', allowNegative: true, thousands:'.', decimal:',', precision: 2});
                    break;
                case UnidadFomento:
                    $(class_txt_money).maskMoney({prefix:'UF ', allowNegative: true, thousands:'.', decimal:',', precision: 2});
                    break;
                case CoronaNoruega:
                    $(class_txt_money).maskMoney({prefix:'NKr ', allowNegative: true, thousands:'.', decimal:',', precision: 2});
                    break;
                case CoronaSueca:
                    $(class_txt_money).maskMoney({prefix:'SKr ', allowNegative: true, thousands:'.', decimal:',', precision: 2});
                    break;
                default: return '';
            }
            $(class_txt_money).maskMoney('mask');
        };

        OrdenesCompraLib.maskMoneyBySymbol = function(currency_symbol,class_input){
            class_input = '.'+class_input;
            switch (currency_symbol){
                case '$':
                    $(class_input).maskMoney({prefix:'$ ', allowNegative: true, thousands:'.', decimal:',', precision: 0});
                    break;
                case 'US$':
                    $(class_input).maskMoney({prefix:'US$ ', allowNegative: true, thousands:'.', decimal:',', precision: 2});
                    break;
                case '€':
                    $(class_input).maskMoney({suffix:'€', allowNegative: true, thousands:'.', decimal:',', precision: 2});
                    break;
                case 'UF':
                    $(class_input).maskMoney({prefix:'UF ', allowNegative: true, thousands:'.', decimal:',', precision: 2});
                    break;
                case 'NKr':
                    $(class_input).maskMoney({prefix:'NKr ', allowNegative: true, thousands:'.', decimal:',', precision: 2});
                    break;
                case 'SKr':
                    $(class_input).maskMoney({prefix:'SKr ', allowNegative: true, thousands:'.', decimal:',', precision: 2});
                    break;
            }
            $(class_input).maskMoney('mask');
        };

        OrdenesCompraLib.filterMoney = function(valor){
            let s = valor + '';
            s = s.split(".").join("");
            s = s.replace('$', '');
            s = s.replace(' ', '');
            s = s.replace('US', '');
            s = s.replace('€', '');
            s = s.replace('NKr', '');
            s = s.replace('SKr', '');
            s = s.replace('UF', '');
            s = s.replace(',', '.');
            return parseFloat(s);
        };

        OrdenesCompraLib.desFilterMoney = function(valor){
            //debugger;
            if($('#currency').val()== PesoChileno){
                valor = parseFloat(valor).toFixed();
            }else{
                valor = parseFloat(valor).toFixed(2);
            }

            let s = valor + '';
            s = s.replace('.', ',');
            return s;
        };

        OrdenesCompraLib.calcularIva = function(monto, accion){
            // 19%
            monto = OrdenesCompraLib.filterMoney(monto);
            let resultado = 0;
            switch (accion) {
                case "agregar":
                    resultado = monto * 1.19;
                    break;
                case "quitar":
                    resultado = monto / 1.19;
                    break;
            }
            //Solo calcula el iva para peso chileno
            if($("#currency").val() != 2){
                resultado = monto;
            }
            return resultado;
        };

        OrdenesCompraLib.calcularHonorario = function(monto, accion){
            // 10%
            monto = OrdenesCompraLib.filterMoney(monto);
            let res = 0;
            switch (accion) {
                case "agregar":
                    res = monto * 1.1;
                    break;
                case "quitar":
                    res = monto / 1.1;
                    break;
            }
            return res;
        };
        
        return OrdenesCompraLib;
    }
    //define globally if it doesn't already exist
    if(typeof(OrdenesCompraLib) === 'undefined'){
        window.OrdenesCompraLib = define_library();
    }
    else{
        console.log("Librería ya definida.");
    }
})(window);