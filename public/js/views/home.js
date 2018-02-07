/**
 * Created by anarela on 16-03-17.
 */
/* ------------------------------------------------------------------------------
 *
 *  # Echarts - pies and donuts
 *
 *  Pies and donuts chart configurations
 *
 *  Version: 1.0
 *  Latest update: August 1, 2015
 *
 * ---------------------------------------------------------------------------- */
var budget = null;
var budgetByMonths = null;
var rose_diagram_hidden = null;
var rose_diagram_hidden_options = null;
var basic_columns = null;
var basic_columns_options = null;

function getBudget(){

    var path = '/getBudgetHome';
    var pathByMonth = '/getBudgetByMonth';

    $.ajax({
        url: path,
        type: 'get',
        async: false,
        success: function (response) {
            budget = response;
            if(response.length != 0){
                // console.log(response);
                $('#grafico').show();
                rose_diagram_hidden.resize();
                if(response.total_budget_available == null){
                    $('#messageNotClosedBudget').html('Área no cerrada aún.');
                    $('#graficoPorMes').hide();
                }else{
                    $('#messageNotClosedBudget').html('');
                    $('#graficoPorMes').show();
                }
            }
        }
    });

    $.ajax({
        url: pathByMonth,
        type: 'get',
        async: false,
        success: function (response) {
            budgetByMonths = response;
            if(response.length != 0){
                // console.log(response);
                basic_columns.resize();
            }
        }
    });

    return true;
};

function getBudgetOtherArea($id){
    var path = '/getBudgetHome/'+ $id;
    var pathByMonth = '/getBudgetByMonth/'+ $id;
    $.ajax({
        url: path,
        type: 'get',
        async: false,
        success: function (response) {
            budget = response;

            if(response.length != 0){

                $('#grafico').hide();
                $('#graficoPorMes').hide();

                var areaName = $('#id_area_selected option:selected').text();
                $('#titulo_grafico').html('Gestión de Presupuesto de ' + areaName + ' para el año 2017');

                rose_diagram_hidden_options.series[0].data = [
                    {value: response.total_budget_available, name: 'No Asignado'},
                    {value: (response.total_budget_initial - response.total_budget_available), name: 'Asignado'}
                ];

                rose_diagram_hidden.setOption(rose_diagram_hidden_options);

                $('#grafico').show();

                if(response.total_budget_available == null){
                    $('#messageNotClosedBudget').html('Área no cerrada aún.');
                }else{
                    $('#messageNotClosedBudget').html('');
                    $('#graficoPorMes').show();
                }
                
            }
        }
    });

    $.ajax({
        url: pathByMonth,
        type: 'get',
        async: false,
        success: function (response) {
            if(response.length != 0){

                var areaName = $('#id_area_selected option:selected').text();
                $('#titulo_grafico_por_mes').html('Gestión de Presupuesto de ' + areaName + ' distribuido por mes para el año 2017');

                basic_columns_options.series[0].data = response;

                basic_columns.setOption(basic_columns_options);

            }
        }
    });

    return true;
}


var initialize = function (ec, limitless) {



    if(countAreasSupervisadas > 0){  //Si el usuario supervisa alguna área


        // Initialize charts
        // ------------------------------

        rose_diagram_hidden = ec.init(document.getElementById('rose_diagram_hidden'), limitless);
        basic_columns = ec.init(document.getElementById('columns_diagram_hidden'), limitless);

        //Pedir mis datos
        if(budget == null){
            getBudget();
        }


        // Charts setup
        // ------------------------------


        //
        // Nightingale roses with hidden labels options
        //

        rose_diagram_hidden_options = {

            // Add title
            title: {
                text: 'Presupuesto No Asignado a Órdenes de Compra',
                subtext: 'Monto disponible para su área asignada',
                x: 'center'
            },

            // Add tooltip
            tooltip: {
                trigger: 'item',
                formatter: "{a} <br/>{b}: +{c}$ ({d}%)"
            },

            // Add legend
            legend: {
                x: 'left',
                y: 'top',
                orient: 'vertical',
                data: ['No Asignado','Asignado']
            },

            // Display toolbox
            toolbox: {
                show: true,
                orient: 'vertical',
                feature: {
                    // mark: {
                    //     show: true,
                    //     title: {
                    //         mark: 'Markline switch',
                    //         markUndo: 'Undo markline',
                    //         markClear: 'Clear markline'
                    //     }
                    // },
                    dataView: {
                        show: true,
                        readOnly: false,
                        title: 'Ver datos',
                        lang: ['Ver datos del presupuesto', 'Cerrar', 'Actualizar']
                    },
                    // magicType: {
                    //     show: true,
                    //     title: {
                    //         pie: 'Cambiar a gráfico de torta',
                    //         funnel: 'Cambiar a gráfico de barras',
                    //     },
                    //     type: ['pie', 'funnel']
                    // },
                    restore: {
                        show: true,
                        title: 'Restaurar'
                    },
                    saveAsImage: {
                        show: true,
                        title: 'Desplegar imagen',
                        lang: ['Guardar']
                    }
                }
            },

            // Enable drag recalculate
            calculable: true,

            // Add series
            series: [
                {
                    name: 'Presupuesto (Asignado a Órdenes de Compra)',
                    type: 'pie',
                    radius: ['15%', '73%'],
                    center: ['50%', '57%'],
                    roseType: 'radius',

                    // Funnel
                    width: '40%',
                    height: '78%',
                    x: '30%',
                    y: '17.5%',
                    max: 450,

                    itemStyle: {
                        normal: {
                            label: {
                                show: false
                            },
                            labelLine: {
                                show: false
                            }
                        },
                        emphasis: {
                            label: {
                                show: true
                            },
                            labelLine: {
                                show: true
                            }
                        }
                    },
                    data: [
                        {value: budget.total_budget_available, name: 'No Asignado'},
                        {value: (budget.total_budget_initial - budget.total_budget_available), name: 'Asignado'}
                    ]
                }
            ]
        };

        //
        // Basic columns options
        //

        basic_columns_options = {

            // Setup grid
            grid: {
                x: 75,
                x2: 40,
                y: 40,
                y2: 25
            },

            // Add tooltip
            tooltip: {
                trigger: 'axis'
            },

            // Add legend
            legend: {
                // data: ['Evaporation', 'Precipitation']
                data: ['Asignado a OC']
            },

            // Enable drag recalculate
            calculable: true,

            // Horizontal axis
            xAxis: [{
                type: 'category',
                data: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic']
            }],

            // Vertical axis
            yAxis: [{
                type: 'value',
                position: 'left'
            }],

            // Add series
            series: [
                {
                    name: 'Asignado a OC',
                    type: 'bar',
                    data: budgetByMonths,
                    itemStyle: {
                        normal: {
                            color: '#b899ef',
                            label: {
                                show: true,
                                textStyle: {
                                    fontWeight: 500
                                }
                            }
                        }
                    },
                    // markLine: {
                    //     data: [{type: 'average', name: 'average'}]
                    // }
                }
                // ,
                // {
                //     name: 'Precipitation',
                //     type: 'bar',
                //     data: [2.6, 5.9, 9.0, 26.4, 58.7, 70.7, 175.6, 182.2, 48.7, 18.8, 6.0, 2.3],
                //     itemStyle: {
                //         normal: {
                //             label: {
                //                 show: true,
                //                 textStyle: {
                //                     fontWeight: 500
                //                 }
                //             }
                //         }
                //     },
                //     markLine: {
                //         data: [{type: 'average', name: 'Average'}]
                //     }
                // }
            ],
            toolbox: {
                show: true,
                orient: 'horizontal',
                feature: {
                    dataView: {
                        show: true,
                        readOnly: false,
                        title: 'Ver datos',
                        lang: ['Ver datos del presupuesto', 'Cerrar', 'Actualizar']
                    },
                    restore: {
                        show: true,
                        title: 'Restaurar'
                    },
                    saveAsImage: {
                        show: true,
                        title: 'Desplegar imagen',
                        lang: ['Guardar']
                    }
                }
            },
        };



        // Apply options
        // ------------------------------


        rose_diagram_hidden.setOption(rose_diagram_hidden_options);
        basic_columns.setOption(basic_columns_options);



        // Resize charts
        // ------------------------------

        window.onresize = function () {
            setTimeout(function (){

                rose_diagram_hidden.resize();
                basic_columns.resize();
            }, 200);
        }
    }
};


$(function () {

    $('#id_area_selected').on('change',function () {
        // console.log($(this).val());
        getBudgetOtherArea($(this).val());
    });

    // Set paths
    // ------------------------------

    require.config({
        paths: {
            echarts: 'assets/template/js/plugins/visualization/echarts'
        }
    });


    // Configuration
    // ------------------------------

    require(
        [
            'echarts',
            'echarts/theme/limitless',
            'echarts/chart/pie',
            'echarts/chart/funnel',
            'echarts/chart/bar',
            'echarts/chart/line',
        ], initialize    );


});
