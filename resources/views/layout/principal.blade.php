<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ordenes Compra - Yapo.cl</title>

    <!-- Global stylesheets -->

    <link href="https://fonts.googleapis.com/css?family=Roboto:400,300,100,500,700,900" rel="stylesheet" type="text/css">
    <link rel="shortcut icon" href="{{url('images/favicon.ico')}}" type="image/x-icon">

    {!!Html::style('/assets/template/css/template.css')!!}
    <!------------------------>
    {!!Html::style('css/app.css')!!}
    <!-- Template JS  -->
    {!!Html::script('/assets/template/js/template.js')!!}
    <!------------------>
    {!!Html::script('js/app.js')!!}

    @section('scripts')
    @show

</head>

<body>
@include('layout.navbar')

<!-- Page container -->
<div class="page-container">
    <!-- Page content -->
    <div class="page-content">
        <!-- Main sidebar -->
        <div class="sidebar sidebar-main">
            <div class="sidebar-content">
                <!-- User menu -->
                <div class="sidebar-user">
                    <div class="category-content">
                        <div class="media">
                            @if(Auth::check())
                            <div class="media-body">
                                <span class="media-heading text-semibold">
                                        <i class="icon-user"></i> {{Auth::user()->firstname}}
                                </span>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                <!-- /user menu -->

                {{--Menu Lateral------}}
                @include('layout.menu')
                {{--------------------}}
            </div>
        </div>
        <!-- /main sidebar -->

        <!-- Main content -->
        <div class="content-wrapper">
            <!-- Page header -->
            <div class="page-header page-header-default">
                <div class="breadcrumb-line">
                    <ul class="breadcrumb">
                        <li><a href="/"><i class="icon-home2 position-left"></i> Inicio</a></li>
                        @php
                            $menu = Session::get('opcion_menu');
                        @endphp
                        @if( $menu !== null)
                            <li class="active">{{$menu}}</li>
                        @endif
                    </ul>
                </div>
            </div>
            <!-- /page header -->

            <!-- Content area -->
            <div class="content">

                <!-- Main charts -->
                <div class="row">
                    <div class="col-lg-12">
                        @if (Session::has('error_message'))
                            <div class="alert alert-danger" role="alert">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <span><b>Error: </b>{{ Session::get('error_message') }}</span>
                            </div>
                        @endif
                        <!-- Contenido -->
                        <div class="panel panel-flat">
                            <div class="panel-body">
                                @yield('content')
                            </div>
                        </div>
                        <!-- /Contenido-->

                    </div>
                </div>

                <!-- Footer -->
                <div class="footer text-muted">
                    &copy; 2017. by <a href="http://www.yapo.cl" target="_blank">Yapo.cl </a>
                </div>
                <!-- /footer -->

            </div>
            <!-- /content area -->

        </div>
        <!-- /main content -->

    </div>
    <!-- /page content -->

    <div class="modal bs-example-modal-sm" id="loading" name="loading" role="dialog" >
        <div class="modal-dialog modal-sm" role="document">
            <div style="background-color: transparent; border: 0px; padding-top:200px;">
                <div class="col-sm-offset-5">
                    <i style="color:aquamarine;" class="fa fa-spinner fa-pulse fa-5x fa-fw"></i>
                    <br />

                </div>
                <div class="col-sm-offset-4">
                    <br />
                    <label style="color: aquamarine; padding-left:5px; font-size: 25px">Procesando...</label>
                </div>

            </div>
        </div>
    </div>

</div>
<!-- /page container -->
{!! Toastr::message() !!}
</body>
</html>
