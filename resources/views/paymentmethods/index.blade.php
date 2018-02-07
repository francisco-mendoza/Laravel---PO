@extends('layout.principal')

@section('content')


<h2 class="page-header">Métodos de Pago</h2>
<br />
<div class="panel panel-default">
    <div class="panel-heading">
        <span class="tituloPantalla">Listado de Métodos de Pago</span>
    </div>
    {{ csrf_field() }}
    <div class="panel-body">
        @permission('crear_metodos')
        <div class="row">
            <a href="{{url('paymentmethods/create')}}" class="btn btn-primary" role="button">
                <i class="fa fa-plus-square-o fa-lg" ></i> Agregar Método de Pago</a>
        </div>
        @endpermission
        <br />
        <div class="">
            <table class="table table-striped table-framed table-hover full-width" id="thegrid">
              <thead>
                <tr>
                    <th>Id Método de Pago</th>
                    <th>Método de Pago</th>
                    <th style="width:100px">Editar</th>
                    <th style="width:100px">Eliminar</th>
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
        var consulta ="{{url('paymentmethods/grid')}}";
        var metodos = "{{url('/paymentmethods')}}";
        var editar = "{{Entrust::can('editar_metodos')}}";
        var eliminar = "{{Entrust::can('eliminar_metodos')}}";

    </script>

    <script type="text/javascript" src="/js/views/paymentMethod.js"></script>

@endsection