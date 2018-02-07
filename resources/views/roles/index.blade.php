@extends('layout.principal')

@section('content')


<h2 class="page-header">Roles</h2>
</br>
<div class="panel panel-default">
    <div class="panel-heading">
        <span class="tituloPantalla">Listado de Roles</span>
    </div>
    {{ csrf_field() }}
    <div class="panel-body">
        @permission('crear_roles')
        <div class="row">
            <a href="{{url('roles/create')}}" class="btn btn-primary " role="button">
                <i class="fa fa-plus-square-o fa-lg" ></i> Agregar Rol</a>
        </div>
        @endpermission
        <br />
        <div class="">
            <table class="table table-striped table-framed table-hover full-width" id="thegrid">
              <thead>
                <tr>
                    <th>Id Rol</th>
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
        var consulta ="{{url('roles/grid')}}";
        var roles = "{{url('/roles')}}";
        var editar = "{{Entrust::can('editar_roles')}}";
        var eliminar = "{{Entrust::can('eliminar_roles')}}";
    </script>
    <script type="text/javascript" src="js/views/roles.js"></script>
@endsection