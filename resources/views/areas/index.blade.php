@extends('layout.principal')

@section('content')


<h2 class="page-header">Áreas </h2>
<br />
<div class="panel panel-default">
    <div class="panel-heading">
        <span class="tituloPantalla">Listado de Áreas</span>
    </div>
    {{ csrf_field() }}
    <div class="panel-body">
        @permission('crear_areas')
            <div class="row">
                <a href="{{url('areas/create')}}" class="btn btn-primary" role="button" id="agregarArea">
                    <i class="fa fa-plus-square-o fa-lg" ></i> Agregar Área </a>
            </div>
        @endpermission
        <br />
        <div class="">
            <table class="table table-striped table-framed table-hover full-width" id="thegrid">
              <thead>
                <tr>
                    <th>Id Area</th>
                    <th>Abreviatura</th>
                    <th>Nombre</th>
                    <th>Gerente</th>
                    <th>Cargo Gerente</th>
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
        var consulta ="{{url('areas/grid')}}";
        var areas = "{{url('/areas')}}";
        var editar = "{{Entrust::can('editar_areas')}}";
        var eliminar = "{{Entrust::can('eliminar_areas')}}";

    </script>
    <script type="text/javascript" src="js/views/areas.js"></script>

@endsection