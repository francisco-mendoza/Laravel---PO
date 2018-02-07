@extends('layout.principal')

@section('content')


<h2 class="page-header">Usuarios</h2>

<br/>
<div class="panel panel-default">
    <div class="panel-heading">
        <span class="tituloPantalla">Listado de Usuarios</span>
    </div>
    {{ csrf_field() }}
    <div class="panel-body">
        @permission('crear_usuarios')
        <div class="row">
            <a href="{{url('users/create')}}" class="btn btn-primary" role="button">
                <i class="fa fa-plus-square-o fa-lg" ></i> Agregar Usuario</a>
        </div>
        @endpermission
        <br />
        <div class="">
            <table class="table table-striped table-framed table-hover full-width" id="thegrid">
              <thead>
                <tr>
                    <th>Id Usuario</th>
                    <th><i class="fa fa-user-o" ></i> Nombre de Usuario</th>
                    <th><i class="fa fa-envelope-o" ></i> Correo</th>
                    <th>Avatar</th>
                    <th>Rol</th>
                    <th>Área</th>
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
        var consulta ="{{url('users/grid')}}";
        var usuarios = "{{url('/users')}}";
        var editar = "{{Entrust::can('editar_usuarios')}}";
        var eliminar = "{{Entrust::can('eliminar_usuarios')}}";
    </script>

    <script type="text/javascript" src="/js/views/users.js"></script>


@endsection