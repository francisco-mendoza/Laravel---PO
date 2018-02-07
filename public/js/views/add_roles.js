/**
 * Created by francisco on 01-06-17.
 */
/* ------------------------------------------------------------------------------
 *
 *  # Dual listboxes
 *
 *  Specific JS code additions for form_dual_listboxes.html page
 *
 *  Version: 1.0
 *  Latest update: Aug 1, 2015
 *
 * ---------------------------------------------------------------------------- */

$(function() {

    // Multiple selection
    var list_box = $('.list-permissions').bootstrapDualListbox({
        preserveSelectionOnMove: 'all',
        moveOnSelect: false,
        infoText: 'Mostrando {0} permisos',
        infoTextFiltered: '<span class="label label-info">Filtrados</span> {0} de {1}',
        infoTextEmpty: '',
        filterPlaceHolder: 'Buscar permiso',
        filterTextClear: 'Mostrar todos',
        selectorMinimalHeight: 300, //Altura lista
        nonSelectedListLabel: '<h6 class="label label-danger">Permisos No Asignados</h6>',
        selectedListLabel: '<h6 class="label label-success">Permisos Asignados</h6>',
    });

    $('.move').tooltip().attr('data-original-title','Mover Seleccionados');
    $('.moveall').tooltip().attr('data-original-title','Mover Todos');
    $('.remove').tooltip().attr('data-original-title','Remover Seleccionados');
    $('.removeall').tooltip().attr('data-original-title','Remover Todos');


    $('#f_roles').on('submit', function() {
        $("#permissions_role").val($("#list_permissions").val());
    });

});