<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Ordenes Compra Yapo.cl</title>

    <!-- Global stylesheets -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,300,100,500,700,900" rel="stylesheet" type="text/css">
    <link rel="shortcut icon" href="{{url('images/favicon.ico')}}" type="image/x-icon">
    {!!Html::style('/assets/template/css/template.css')!!}
    {!!Html::style('css/app.css')!!}
    <!-- /global stylesheets -->

    <!-- Core JS files -->
    {!!Html::script('/assets/template/js/template.js')!!}
    {!!Html::script('js/views/login.js')!!}
    <!-- /core JS files -->


</head>

<body class="login-container">


<!-- Page container -->
<div class="page-container">

    <!-- Page content -->
    <div class="page-content">

        <!-- Main content -->
        <div class="content-wrapper">


            <!-- Content area -->
            <div class="content">
                @if (Session::has('error_message'))
                    <div class="alert alert-danger" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <span><b>Error al iniciar sesión: </b>{{ Session::get('error_message') }}</span>
                    </div>
                @endif



                <!-- Simple login form -->
                <form action="index.html">
                    <div class="panel panel-body login-form">
                        <div class="text-center">
                            <div class="icon-object border-slate-300 text-slate-300" style="border-color:#fff;padding: 0">
                                <img src="images/IconoOrdenCompra.png" alt="">
                                {{--<i class="icon-user"></i>--}}
                            </div>
                            {{--<i class="fa fa-user-circle-o fa-5x" aria-hidden="true"></i>--}}

                            <div style="font-size: x-large;margin-bottom: 0 !important;" class="content-group">ROSS</div>
                            <h5 style="margin-top: 0 !important;" class="content-group">Ordenes de Compra <small class="display-block"></small></h5>

                        </div>

                        {{--<div class="form-group has-feedback has-feedback-left">--}}
                            {{--<input type="text" class="form-control" placeholder="Usuario">--}}
                            {{--<div class="form-control-feedback">--}}
                                {{--<i class="icon-user text-muted"></i>--}}
                            {{--</div>--}}
                        {{--</div>--}}

                        {{--<div class="form-group has-feedback has-feedback-left">--}}
                            {{--<input type="text" class="form-control" placeholder="Contraseña">--}}
                            {{--<div class="form-control-feedback">--}}
                                {{--<i class="icon-lock2 text-muted"></i>--}}
                            {{--</div>--}}
                        {{--</div>--}}

                        <div class="form-group">
                            <a class="btn btn-danger btn-block " name="login" id="btn_login">
                                <i class="fa fa-google fa-lg" id="icon_login" aria-hidden="true"></i>
                                <span id="enter_label"> Entrar con Google</span>
                            </a>
                            {{--<button type="submit" class="btn btn-primary btn-block">Entrar <i class="icon-circle-right2 position-right"></i></button>--}}
                        </div>
                    </div>
                </form>
                <!-- /simple login form -->

            </div>
            <!-- /content area -->

        </div>
        <!-- /main content -->

    </div>
    <!-- /page content -->

</div>
<!-- /page container -->

</body>
</html>
