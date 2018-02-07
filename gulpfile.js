const elixir = require('laravel-elixir');

require('laravel-elixir-vue-2');
process.env.DISABLE_NOTIFIER = true;
/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Sass
 | file for our application, as well as publishing vendor resources.
 |
 */

elixir(mix => {
    // mix.sass('app.scss')
    //    .webpack('app.js');
    mix.styles([
        '../template/css/icons/icomoon/styles.css',
        '../template/css/bootstrap.css',
        '../template/css/core.css',
        '../template/css/components.css',
        '../template/css/colors.css',
        '../template/css/icons/fontawesome/css/font-awesome.min.css',
    ],'public/assets/template/css/template.css').browserSync({
        notify: false,
        proxy:'local.ordenescompra.cl',
    });

    mix.styles([
        // 'pace.css',
        'dataTables.bootstrap.min.css',
        'select.dataTables.min.css',
        'toastr.min.css',
        'layout.css',
        'ordenescompra.css',
        'fileinput.min.css',
        'sweetalert.css',
    ],'public/css/app.css');

    //---------- JS TEMPLATE ----------------//
    mix.scripts([
        '../template/js/plugins/loaders/pace.min.js',
        '../template/js/core/libraries/jquery.min.js',
        '../template/js/core/libraries/bootstrap.min.js',
        '../template/js/plugins/loaders/blockui.min.js',
        '../template/js/plugins/forms/styling/uniform.min.js',
        '../template/js/plugins/forms/styling/switchery.min.js',
        '../template/js/plugins/forms/styling/switch.min.js',
        '../template/js/plugins/visualization/d3/d3.min.js',
        '../template/js/plugins/visualization/d3/d3_tooltip.js',
        '../template/js/plugins/forms/selects/bootstrap_multiselect.js',
        '../template/js/plugins/ui/moment/moment.min.js',
        '../template/js/plugins/pickers/daterangepicker.js',
        '../template/js/core/app.js',

    ],'public/assets/template/js/template.js');

    //---------- JS APP ----------------//
    mix.scripts([
        // 'pace.min.js',
        'handlebars.js',
        'datatables.min.js',
        'jquery.dataTables.min.js',
        'dataTables.bootstrap.min.js',
        'dataTables.select.min.js',
        'toastr.min.js',
        'sweetalert.min.js',
        'ordenescompra.js',
        'ordenesCompraLibrary.js',
        'views/principal.js',
        'validator.js',
        'typeahead.js',
        'jquery.maskMoney.js',
        'fileinput.js',
        'jquery.number.js',
        'es.js',
        'locales/es.js',
        'duallistbox.min.js',
        'steps.min.js',

    ],'public/js/app.js');


});
