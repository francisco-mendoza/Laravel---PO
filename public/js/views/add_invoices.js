//Id's Currency
const PesoChileno   = 2;
const Dolar         = 3;
const Euro          = 4;
const UnidadFomento = 5;
const CoronaNoruega = 6;
const CoronaSueca   = 7;


$(document).ready(function() {

    // Show form
    var form = $(".steps-basic").show();

    console.log(monto_minimo);


    // Basic wizard setup
    $(".steps-basic").steps({
        headerTag: "h6",
        bodyTag: "fieldset",
        transitionEffect: "fade",
        titleTemplate: '<span class="number">#index#</span> #title#',
        labels: {
            next: 'Siguiente <i class="icon-arrow-right14 position-right"></i>',
            previous: '<i class="icon-arrow-left13 position-left"></i> Anterior',
            finish: 'Guardar Factura <i class="icon-check position-right"></i>'
        },
        onStepChanging: function (event, currentIndex, newIndex) {

            // Allways allow previous action even if the current form is not valid!
            if (currentIndex > newIndex) {
                return true;
            }
            // Forbid next action on "Warning" step if the user is to young
            let total = OrdenesCompraLib.filterMoney($("#total").val());
            if (total == 0) {
                $("#total").val("");
            }


            // Needed in some cases if the user went back (clean up)
            if (currentIndex < newIndex) {

                // To remove error styles
                form.find(".body:eq(" + newIndex + ") label.error").remove();
                form.find(".body:eq(" + newIndex + ") .error").removeClass("error");
            }


            return true;
        },
        onStepChanged: function(event, currentIndex, newIndex){
            if(currentIndex===0) { //Voy a generar data de OC
                $('.steps-basic').find('.actions .disabled').hide();
            }
        },
        onFinishing: function (event, currentIndex)
        {
            var form = $(this);

            // Disable validation on fields that are disabled.
            // At this point it's recommended to do an overall check (mean ignoring only disabled fields)
            form.validate().settings.ignore = ":disabled";

            // Start validation; Prevent form submission if false
            return form.valid();
        },
        onFinished: function (event, currentIndex) {
            // Forbid next action on "Warning" step if the user is to young
            let total = OrdenesCompraLib.filterMoney($("#total").val());
            if (total == 0) {
                $("#total").val("");
            }
             $("#f_invoice").submit();
        }
    });

    $('.steps-basic').find('.actions .disabled').hide();


    $.validator.addMethod("minTotal",function(value, element){

        if(monto_minimo != "undefined" && monto_minimo != 0){
            if(OrdenesCompraLib.filterMoney($("#total").val()) < parseFloat(monto_minimo)){
                return false;
            }
        }

        return true;
    },"Existen OC asociadas a esta factura, monto mínimo permitido es " + monto_minimo);

    // Initialize validation
    $(".steps-basic").validate({
        ignore: 'input[type=hidden], .select2-search__field', // ignore hidden fields
        errorClass: 'validation-error-label',
        successClass: 'validation-valid-label',
        highlight: function(element, errorClass) {
            $(element).removeClass(errorClass);
        },
        unhighlight: function(element, errorClass) {
            $(element).removeClass(errorClass);
        },

        // Different components require proper error label placement
        errorPlacement: function(error, element) {

            // Styled checkboxes, radios, bootstrap switch
            if (element.parents('div').hasClass("checker") || element.parents('div').hasClass("choice") || element.parent().hasClass('bootstrap-switch-container') ) {
                if(element.parents('label').hasClass('checkbox-inline') || element.parents('label').hasClass('radio-inline')) {
                    error.appendTo( element.parent().parent().parent().parent() );
                }
                else {
                    error.appendTo( element.parent().parent().parent().parent().parent() );
                }
            }

            // Unstyled checkboxes, radios
            else if (element.parents('div').hasClass('checkbox') || element.parents('div').hasClass('radio')) {
                error.appendTo( element.parent().parent().parent() );
            }

            // Input with icons and Select2
            else if (element.parents('div').hasClass('has-feedback') || element.hasClass('select2-hidden-accessible')) {
                error.appendTo( element.parent() );
            }

            // Inline checkboxes, radios
            else if (element.parents('label').hasClass('checkbox-inline') || element.parents('label').hasClass('radio-inline')) {
                error.appendTo( element.parent().parent() );
            }

            // Input group, styled file input
            else if (element.parent().hasClass('uploader') || element.parents().hasClass('input-group')) {
                error.appendTo( element.parent().parent() );
            }

            else {
                error.insertAfter(element);
            }
        },
        rules: {
            id_invoice:{
                required:true,
            },
            id_provider:{
                required:true,
            },
            billing_month:{
                required:true
            },
            billing_year:{
                required:true
            },
            currency:{
                required:true
            },
            total:{
                required:true,
                minTotal:true
            },



        },
        messages:{
            "id_invoice" : {
                required: "Debes ingresar el ID de la Factura"
            },
            "id_provider" : {
                required: "Debes ingresar el Proveedor"
            },
            "billing_month":{
                required: "Ingrese mes de Facturación"
            },
            "billing_year":{
                required: "Ingrese año de Facturación"
            },
            "currency":{
                required: "Seleccione tipo de moneda"
            },
            "total":{
                required: "Ingrese total de factura"
            },


        }
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
        url:   '/getSelectProviders',
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
        }
    });

    OrdenesCompraLib.maskMoney($("#currency").val());

    //Formato Monedas
    $('#currency').change(function() {
        $('.money,#total,#total_impuesto').val(0);
        OrdenesCompraLib.maskMoney($("#currency").val());
    });








});
