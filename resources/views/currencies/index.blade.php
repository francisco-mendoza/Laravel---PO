@extends('layout.principal')

@section('content')


<h2 class="page-header">{{ ucfirst('Monedas') }}</h2>
<br>
<div class="panel panel-default">
    <div class="panel-heading">
        <span class="tituloPantalla">Lista de Monedas</span>
    </div>
    {{ csrf_field() }}
    <div class="panel-body">
        @permission('crear_monedas')
            <div class="row">
                <a href="{{url('currencies/create')}}" class="btn btn-primary" role="button">
                    <i class="fa fa-plus-square-o fa-lg" ></i> Nueva Moneda</a>
            </div>
        @endpermission
        <br>
        <div class="">
            <table class="table table-striped table-framed table-hover full-width" id="thegrid">
              <thead>
                <tr>
                    <th>Id Moneda</th>
                    <th>Nombre</th>
                    <th>Sigla</th>
                    <th>Código</th>
                    <th style="width:50px">Editar</th>
                    <th style="width:50px">Eliminar</th>
                </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
        </div>

    </div>
</div>

@endsection

@section('scripts')
    <script type="text/javascript" >
        var consulta ="{{url('currencies/grid')}}";
        var currencies = "{{url('/currencies')}}";
        var editar = "{{Entrust::can('editar_monedas')}}";
        var eliminar = "{{Entrust::can('eliminar_monedas')}}";
    </script>
    {{Html::script('js/views/currencies.js')}}
@endsection