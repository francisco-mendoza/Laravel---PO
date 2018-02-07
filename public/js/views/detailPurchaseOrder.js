/**
 * Created by anarela on 07-02-17.
 */

var mensajeRechazo = null;

$(document).ready(function(){

    if(action== "print"){
        imprSelec("tablaImprimir", "footer");
    }

    if(action=="validate"){
        $('#botonesAprobacion').removeAttr('hidden');
        $('#botonesConsulta').hide();
    }

    $("#rechazar").on("click",function(){

        var url = "/rejectPurchaseOrder/" + idOrder;

        swal({
                title: "Feedback!",
                text: "Detalle el motivo del rechazo:",
                type: "input",
                showCancelButton: true,
                cancelButtonText: "Cerrar",
                closeOnConfirm: false,
                animation: "slide-from-top",
                inputPlaceholder: "Ingrese justificación...",
                id:"txtJustificacion"
            },
            function(inputValue){
                if (inputValue === false) return false;

                if (inputValue === "") {
                    swal.showInputError("Debe ingresar un motivo!");
                    return false
                }

                mensajeRechazo =  inputValue;


                $.ajax({
                    url: url,
                    type: 'GET',
                    data: {
                        "id": idOrder,
                        "_method": 'GET',
                        // "_token": tok,
                        "mensajeRechazo":mensajeRechazo,
                    },
                    success: function ()
                    {
                        swal({ title: "Orden Rechazada!", text: "Se enviará un correo con su mensaje: " + inputValue, type: "success"},function() {

                            window.location.href = listadoAprobar;
                        });

                    },
                    error: function(errorThrown) {
                        swal("Error!", "Ocurrió un error al intentar aprobar la orden de compra. Intente nuevamente", "error");
                    }

                });


            });
    });

    $("#aprobar").on("click",function(){
        
        var url = "/approvePurchaseOrder/" + idOrder;

        swal({
                title: "Está seguro?",
                text: "Valide el contenido de la orden antes de aprobar.",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#4CAF50",
                confirmButtonText: "Aprobar!",
                cancelButtonText: "Cerrar",
                closeOnConfirm: false
            },
            function(){
                $.ajax({
                    url: url,
                    type: 'GET',
                    data: {
                        "id": idOrder,
                        "_method": 'GET',
                        // "_token": tok,
                    },
                    success: function ()
                    {
                        swal({ title: "Aprobada!", text: "La orden de compra ha sido aprobada.", type: "success"},function() {

                            window.location.href = listadoAprobar;
                        });

                    },
                    error: function(errorThrown) {
                        swal("Error!", "Ocurrió un error al intentar aprobar la orden de compra. Intente nuevamente", "error");
                    }
                
                });
            });

    });

});

function imprSelec(nombre, nombreFooter) {
    var ficha = document.getElementById(nombre);
    var footer = document.getElementById(nombreFooter);
    var ventimp = window.open(' ', 'printwindow');
    console.log(ficha.innerHTML);
    ventimp.document.write('<html><head><link href="../../css/app.css" rel="stylesheet" type="text/css">');
    ventimp.document.write('<style>body { font-size: 11px !important;} @page { size: auto;  margin: 7mm; }</style>')
    ventimp.document.write('</head><body>');
    ventimp.document.write('<table style="width: 100%;table-layout: fixed;font-family: Helvetica Neue, Helvetica, Arial, sans-serif; font-size: 11px;">');
    ventimp.document.write( ficha.innerHTML );
    ventimp.document.write('</table>');

    //Agregar el footer
    ventimp.document.write('<footer style="position:fixed;bottom:0;">');
    ventimp.document.write('<table style="width: 100%;table-layout: fixed;font-family: Helvetica Neue, Helvetica, Arial, sans-serif; font-size: 11px;">');
    ventimp.document.write(footer.innerHTML);
    ventimp.document.write('</table>');
    ventimp.document.write('</footer>')

    ventimp.document.write('</body></html>');
    ventimp.document.close();
    ventimp.print( );
    ventimp.close();

    return true;
}