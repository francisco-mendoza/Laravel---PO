@extends('layout.principal')

@section('content')


<h2 class="page-header">Opciones de Menú</h2>
<br>
<div class="panel panel-default">
    <div class="panel-heading">
        <span class="tituloPantalla">Agregar/Modificar Opcion de Menú</span>
    </div>

    <div class="panel-body">

        <form action="{{ url('/menuOptions'.( isset($model) ? "/" . $model->id_menu : "")) }}" method="POST" id="f_menu_options" class="form-horizontal">
            {{ csrf_field() }}

            @if (isset($model))
                <input type="hidden" name="_method" value="PATCH">
                <input type="hidden" name="id_menu" id="id_menu" class="form-control" value="{{$model['id_menu'] or ''}}">
            @endif

            <div class="form-group">
                <label for="name_option" class="col-sm-3 control-label">Nombre Opción</label>
                <div class="col-sm-6">
                    <input type="text" name="name_option" id="name_option" required class="form-control" value="{{$model['name_option'] or ''}}">
                    <div class="help-block with-errors"></div>
                </div>
            </div>
            <div class="form-group">
                <label for="order_option" class="col-sm-3 control-label">Orden</label>
                <div class="col-sm-2">
                    <input type="number" name="order_option" id="order_option" required class="form-control" value="{{$model['order_option'] or ''}}">
                    <div class="help-block with-errors"></div>
                </div>
            </div>
            <div class="form-group">
                <label for="option_route" class="col-sm-3 control-label">Ruta</label>
                <div class="col-sm-6">
                    <input type="text" name="option_route" id="option_route" required class="form-control" value="{{$model['option_route'] or ''}}">
                    <div class="help-block with-errors"></div>
                </div>
            </div>
            <div class="form-group">
                <label for="option_icon" class="col-sm-3 control-label">Icono</label>
                <div class="col-sm-6">
                    <div id="custom-templates">
                        <input class="form-control typeahead" required data-contains type="text" name="option_icon" id="option_icon" placeholder="Click para buscar iconos" value="{{$model['option_icon'] or ''}}">
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="col-sm-offset-3 col-sm-6">
                    <a class="btn btn-default" href="{{ url('/menuOptions') }}"><i class="glyphicon glyphicon-chevron-left"></i> Atrás</a>
                    <button type="submit" class="btn btn-success" id="guardar">
                        <i class="fa fa-save"></i> Guardar
                    </button>

                </div>
            </div>
        </form>

    </div>
</div>

@endsection

@section('scripts')
    {{Html::script('js/views/menu_options.js')}}
@endsection