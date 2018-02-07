@extends('layout.principal')

@section('content')
    <h1>Sistema de Órdenes de Compra </h1>
    <br />
    @if($esAprobador)
    <div class="row">
        <div class="content-group">
            <div class="row row-seamless btn-block-group">
                <div class="col-lg-4">
                    <a href="{{route('consultarOrdenes')}}" class="btn btn-success  btn-block btn-float btn-float-lg ">
                        <i class="fa fa-search fa-2x "></i>
                        <span><b>Consultar Órdenes</b></span>
                    </a>
                </div>
                <div class="col-lg-4">
                    <a href="{{route('crearOrden')}}" class="btn btn-info btn-block btn-float btn-float-lg">
                        <i class="fa fa-shopping-cart fa-5x "></i>
                        <span><b>Crear Orden de Compra</b></span>
                    </a>
                </div>
                <div class="col-lg-4">
                    <a href="{{route('aprobarOrdenes')}}" class="btn bg-pink-400 btn-block btn-float btn-float-lg">
                        <i class="icon-check "></i>
                        <span><b>Aprobar Órdenes</b></span>
                    </a>
                </div>
            </div>
        </div>
    </div>
    @else
        <div class="row">
            <div class="content-group">
                <div class="row row-seamless btn-block-group">
                    <div class="col-lg-2"></div>
                    <div class="col-lg-4">
                        <a href="{{route('consultarOrdenes')}}"  class="btn btn-success btn-block btn-float btn-float-lg">
                            <i class="fa fa-search fa-5x"></i>
                            <span><b>Consultar Órdenes</b></span>
                        </a>
                    </div>

                    <div class="col-lg-4">
                        <a  href="{{route('crearOrden')}}" class="btn btn-info btn-block btn-float btn-float-lg">
                            <i class="fa fa-shopping-cart fa-5x"></i>
                            <span><b>Crear Orden de Compra</b></span>
                        </a>
                    </div>
                    <div class="col-lg-2"></div>
                </div>
            </div>
        </div>
    @endif
    <br/>

        <div class="panel panel-flat " id="grafico" hidden>
            <div class="panel-heading">
                <h2 class="panel-heading" id="titulo_grafico">{{$titulo}}</h2>
                @if(count($areasManager)>1)
                    <div>
                        {{Form::select('id_area_selected', $areasManager, null , ['class' => 'form-control', 'style' => 'max-width:200px !important;','name'=>'id_area_selected', 'id'=>'id_area_selected', 'placeholder' => 'Seleccione un área'])  }}
                    </div>
                @endif
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                    </ul>
                </div>
            </div>
            <div class="panel-body" style="display: block;">
                    <div class="chart-container has-scroll" >
                        <div class="chart has-fixed-height has-minimum-width" id="rose_diagram_hidden"></div>
                        <div class="center-column alert-warning" id="messageNotClosedBudget"></div>
                        <br />

                    </div>
            </div>
        </div>
        <div class="panel panel-flat " id="graficoPorMes" hidden>
            <div class="panel-heading">
                <h2 class="panel-heading" id="titulo_grafico_por_mes">{{$titulo2}}</h2>
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                    </ul>
                </div>
            </div>
            <div class="panel-body" style="display: block;">
                <div class="chart-container" >
                    <div class="chart has-fixed-height " id="columns_diagram_hidden"></div>
                    <br />
                </div>
            </div>
        </div>
@endsection

@section('scripts')
    <script type="text/javascript" >
        var countAreasSupervisadas ="{{count($areasManager)}}";

    </script>
    <script type="text/javascript" src="../assets/template/js/plugins/visualization/echarts/echarts.js"></script>
    <script type="text/javascript" src="../js/views/home.js"></script>
@endsection
