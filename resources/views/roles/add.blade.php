@extends('layout.principal')

@section('content')


<h2 class="page-header">Roles </h2>
<br />
<div class="panel panel-default">
    <div class="panel-heading">
        <span class="tituloPantalla">Agregar/Editar Rol</span>   </div>

    <div class="panel-body">
                
        <form action="{{ url('/roles'.( isset($model) ? "/" . $model->id: "")) }}" method="POST" class="form-horizontal" id="f_roles">
            {{ csrf_field() }}

            @if (isset($model))
                <input type="hidden" name="_method" value="PATCH">
                <input type="hidden" name="id_role" id="id_role" value="{{$model->id}}">
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

            <div class="form-group ">
                <label for="is_default" class="col-sm-3 control-label">Es rol por defecto? </label>

                <div class=" col-sm-6 checker border-info-600 text-info-800">
                    {{ Form::checkbox('is_default', null,isset($model) ?  $model->is_default : false, ['class' => 'styled '] ) }}
                </div>

            </div>

            <hr>
            <span class=" tituloPantalla">Permisos de Rol</span>
            <br><br>

            <div class="form-group ">
                <input type="hidden" id="permissions_role" name="permissions_role[]">
                <select multiple="multiple" class="form-control list-permissions" name="list_permissions[]" id="list_permissions" style="display: none;">
                    @foreach($options as $permiso)
                        @php
                            if(isset($optionsRole)){
                                $is_checked = $optionsRole->contains('id_permiso', $permiso->id_permiso);
                            }else{
                                $is_checked = '';
                            }
                        $selected = $is_checked ? 'selected': '';
                        @endphp

                        <option value="{{$permiso->id_permiso}}" {{$selected}}>{{$permiso->description}}</option>

                    @endforeach

                </select>
            </div>

            <br><br>

            <div class="form-group">
                <div class="col-sm-offset-3 col-sm-6">
                    <a class="btn btn-default" href="{{ url('/roles') }}"><i class="glyphicon glyphicon-chevron-left"></i> Atrás</a>
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

    <script type="text/javascript" src="/js/views/add_roles.js"></script>

@endsection