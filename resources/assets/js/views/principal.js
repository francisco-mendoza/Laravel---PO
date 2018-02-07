/**
 * Created by francisco on 13-01-17.
 */
$(document).ready(function(){

    //Agrega active al menu seleccionado
    var path = window.location.pathname;
    $("[data-role='"+path+"']").addClass('active');
    

});