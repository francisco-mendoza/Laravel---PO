@extends('layout.principal')

@section('content')


<h2 class="page-header">Facturas</h2>
<br />
<div class="panel panel-default">
    <div class="panel-heading">
        Listado de Facturas
    </div>
    {{ csrf_field() }}
    <div class="panel-body">
        @permission('crear_facturas')
            <div class="row">
                <a href="{{url('invoices/create')}}" class="btn btn-primary" role="button" id="agregarFactura">
                    <i class="fa fa-plus-square-o fa-lg" ></i> Agregar Factura </a>
            </div>
        @endpermission
        <br />
        <div class="">
            <table class="table table-striped table-framed table-hover full-width" id="thegrid">
              <thead>
                <tr>
                    <th >N° Factura</th>
                    <th>Proveedor</th>
                    <th style="width:190px" class="center-column">Total</th>
                    <th style="width:70px">Editar</th>
                    <th style="width:70px">Eliminar</th>
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
    <script type="text/javascript">
        var consulta ="{{url('invoices/grid')}}";
        var facturas = "{{url('/invoices')}}";
        var editar = "{{Entrust::can('editar_facturas')}}";
        var eliminar = "{{Entrust::can('eliminar_facturas')}}";
    </script>
    <script type="text/javascript" src="js/views/invoices.js"></script>
@endsection