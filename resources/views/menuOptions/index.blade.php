@extends('layout.principal')

@section('content')


<h2 class="page-header">Opciones de Menú</h2>
<br>
<div class="panel panel-default">
    <div class="panel-heading">
        Lista de Opciones de Menú
    </div>
    {{ csrf_field() }}
    <div class="panel-body">
        @permission('crear_menus')
        <div class="row">
            <a href="{{url('menuOptions/create')}}" class="btn btn-primary" role="button">
                <i class="fa fa-plus-square-o fa-lg" ></i> Nuevo Opcion Menú</a>
        </div>
        @endpermission
        <br>
        <div class="">
            <table class="table table-striped table-framed table-hover full-width" id="thegrid">
              <thead>
                <tr>
                    <th>Id Menu</th>
                    <th>Nombre Opción</th>
                    <th>Orden</th>
                    <th>Ruta</th>
                    <th>Icono</th>
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
    <script type="text/javascript">
        var consulta ="{{url('menuOptions/grid')}}";
        var menu_options = "{{url('/menuOptions')}}";
        var editar = "{{Entrust::can('editar_menus')}}";
        var eliminar = "{{Entrust::can('eliminar_menus')}}";
    </script>
    {{Html::script('js/views/menu_options.js')}}
@endsection