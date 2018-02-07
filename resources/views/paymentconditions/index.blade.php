@extends('layout.principal')

@section('content')


<h2 class="page-header">Condiciones de Pago</h2>
<br />
<div class="panel panel-default">
    <div class="panel-heading">
        <span class="tituloPantalla">Listado de Condiciones de Pago </span>
    </div>
    {{ csrf_field() }}
    <div class="panel-body">
        @permission('crear_condiciones')
        <div class="row">
            <a href="{{url('paymentconditions/create')}}" class="btn btn-primary" role="button" id="createPaymentCondition" name="createPaymentCondition">
                <i class="fa fa-plus-square-o fa-lg" ></i> Agregar Condición de Pago</a>
        </div>
        @endpermission
        <br />
        <div class="">
            <table class="table table-striped table-framed table-hover full-width" id="thegrid">
              <thead>
                <tr>
                    <th>Id Condición de Pago</th>
                    <th>Condición de Pago</th>
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
        var consulta ="{{url('paymentconditions/grid')}}";
        var condiciones = "{{url('/paymentconditions')}}";
        var editar = "{{Entrust::can('editar_condiciones')}}";
        var eliminar = "{{Entrust::can('eliminar_condiciones')}}";

    </script>

    <script type="text/javascript" src="/js/views/paymentCondition.js"></script>

@endsection