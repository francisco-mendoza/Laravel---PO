@extends('layout.principal')

@section('content')


    <h2 class="page-header">Permisos </h2>
    <br />
    <div class="panel panel-default">
        <div class="panel-heading">
            <span class="tituloPantalla">Agregar/Editar Permiso</span>   </div>

        <div class="panel-body">

            <form action="{{ url('/permissions'.( isset($model) ? "/" . $model->id: "")) }}" method="POST" class="form-horizontal" id="f_permissions">
                {{ csrf_field() }}

                @if (isset($model))
                    <input type="hidden" name="_method" value="PATCH">
                    <input type="hidden" name="id_permission" id="id_permission" value="{{$model->id}}">
                @endif


                <div class="form-group">
                    <label for="description" class="col-sm-3 control-label">Nombre</label>
                    <div class="col-sm-6">
                        <input type="text" name="name" id="name" class="form-control" value="{{$model->name or ''}}" {{isset($model) ? "readonly" : ""}}>
                        @if($errors->has('name'))
                            <span class="text-danger">{{ $errors->first('name') }}</span>
                        @endif
                    </div>
                </div>
                <div class="form-group">
                    <label for="description" class="col-sm-3 control-label">Nombre a mostrar</label>
                    <div class="col-sm-6">
                        <input type="text" name="display_name" id="display_name" class="form-control" value="{{$model->display_name or ''}}">
                        @if($errors->has('display_name'))
                            <span class="text-danger">{{ $errors->first('display_name') }}</span>
                        @endif
                    </div>
                </div>

                <div class="form-group">
                    <label for="description" class="col-sm-3 control-label">Descripción</label>
                    <div class="col-sm-6">
                        <input type="text" name="description" id="description" class="form-control" value="{{$model->description or ''}}">
                        @if($errors->has('description'))
                            <span class="text-danger">{{ $errors->first('description') }}</span>
                        @endif
                    </div>
                </div>


                <hr>

                <br><br>

                <div class="form-group">
                    <div class="col-sm-offset-3 col-sm-6">
                        <a class="btn btn-default" href="{{ url('/permissions') }}"><i class="glyphicon glyphicon-chevron-left"></i> Atrás</a>
                        <button type="submit" class="btn btn-success">
                            <i class="fa fa-save"></i> Guardar
                        </button>

                    </div>
                </div>
            </form>

        </div>
    </div>

@endsection

@section('scripts')


@endsection