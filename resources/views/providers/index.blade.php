@extends('layout.principal')

@section('content')


<h2 class="page-header" xmlns="http://www.w3.org/1999/html">Proveedores</h2>
<br />
<div class="panel panel-default">
    <div class="panel-heading">
        <span class="tituloPantalla">Listado de Proveedores</span>
    </div>
    {{ csrf_field() }}
    <div class="panel-body">
        @permission('crear_proveedores')
        <div class="row">
            <a href="{{url('providers/create')}}" class="btn btn-primary" role="button">
                <i class="fa fa-plus-square-o fa-lg" ></i> Agregar Proveedor</a>
        </div>
        @endpermission
        <br />
        <div class="">
            <table class="table table-striped table-framed table-hover full-width" id="thegrid">
              <thead>
                <tr>
                    <th>Id Proveedor</th>
                    <th>Razón Social</th>
                    <th style="width: 80px">RUT</th>
                    <th>Dirección</th>
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
        var consulta ="{{url('providers/grid')}}";
        var proveedor = "{{url('/providers')}}";
        var editar = "{{Entrust::can('editar_proveedores')}}";
        var eliminar = "{{Entrust::can('eliminar_proveedores')}}";

    </script>

    <script type="text/javascript" src="js/views/providers.js"></script>

@endsection