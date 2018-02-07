@extends('layout.principal')

@section('content')


    <h2 class="page-header">Permisos</h2>
    </br>
    <div class="panel panel-default">
        <div class="panel-heading">
            <span class="tituloPantalla">Listado de Permisos</span>
        </div>
        {{ csrf_field() }}
        <div class="panel-body">
            @permission('crear_permisos')
            <div class="row">
                <a href="{{url('permissions/create')}}" class="btn btn-primary " role="button">
                    <i class="fa fa-plus-square-o fa-lg" ></i> Agregar Permiso</a>
            </div>
            @endpermission
            <br />
            <div class="">
                <table class="table table-striped table-framed table-hover full-width" id="thegrid">
                    <thead>
                    <tr>
                        <th>Id Permiso</th>
                        <th>Nombre</th>
                        <th>Nombre a Mostrar</th>
                        <th>Descripción</th>
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
        var consulta ="{{url('permissions/grid')}}";
        var permissions = "{{url('/permissions')}}";
        var editar = "{{Entrust::can('editar_permisos')}}";
        var eliminar = "{{Entrust::can('eliminar_permisos')}}";

    </script>
    {{--<script type="text/javascript" src="js/views/roles.js"></script>--}}
    <script type="text/javascript" src="js/views/permissions.js"></script>
@endsection