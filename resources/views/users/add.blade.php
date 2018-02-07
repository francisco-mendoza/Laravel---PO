@extends('layout.principal')


@section('content')


<h2 class="page-header">Usuario</h2>
<br/>
<div class="panel panel-default">
    <div class="panel-heading">
        <span class="tituloPantalla">Agregar/Editar Usuario</span>    </div>

    <div class="panel-body">
                
        <form id="f_usuario" action="{{ url('/users'.( isset($model) ? "/" . $model->id_user : "")) }}" method="POST" class="form-horizontal">
            {{ csrf_field() }}

            @if (isset($model))
                <input type="hidden" name="_method" value="PATCH">
                <input type="hidden" name="id_user" id="id_user" value="{{$model->id_user}}">
            @endif


            <div class="form-group">
                <label for="username" class="col-sm-3 control-label">Nombre de Usuario</label>
                <div class="col-sm-6">
                    <input type="text" name="username" id="username" class="form-control" value="{{$model->username or ''}}" required maxlength="60">
                    <div class="help-block with-errors"></div>
                    @if ($errors->has('username'))
                        <div class="alert alert-danger">
                            {{$errors->first('username')}}
                        </div>
                    @endif
                </div>
            </div>
             <div class="form-group">
                <label for="firstname" class="col-sm-3 control-label">Nombre</label>
                <div class="col-sm-6">
                    <input type="text" name="firstname" id="firstname" class="form-control" value="{{$model->firstname or ''}}" required maxlength="128">
                    <div class="help-block with-errors"></div>
                    @if ($errors->has('firstname'))
                        <div class="alert alert-danger">
                            {{$errors->first('firstname')}}
                        </div>
                    @endif
                </div>
            </div>
             <div class="form-group">
                <label for="lastname" class="col-sm-3 control-label">Apellido</label>
                <div class="col-sm-6">
                    <input type="text" name="lastname" id="lastname" class="form-control" value="{{$model->lastname or ''}}" required maxlength="128">
                    <div class="help-block with-errors"></div>
                    @if ($errors->has('lastname'))
                        <div class="alert alert-danger">
                            {{$errors->first('lastname')}}
                        </div>
                    @endif
                </div>
            </div>
             <div class="form-group">
                <label for="email" class="col-sm-3 control-label">Correo</label>
                <div class="col-sm-6">
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-envelope-o" ></i></span>
                        <input type="email" name="email" id="email" class="form-control" value="{{$model->email or ''}}" required maxlength="60">
                    </div>
                    <div class="help-block with-errors"></div>
                    @if ($errors->has('email'))
                        <div class="alert alert-danger">
                            {{$errors->first('email')}}
                        </div>
                    @endif
                </div>
            </div>
             <div class="form-group">
                <label for="id_area" class="col-sm-3 control-label">Area Asignada</label>
                <div class="col-sm-6">
                    {{ Form::select('id_area', $areas, isset($model->id_area) ? $model->id_area : null , ['class' => 'form-control', 'id' => 'id_area','required' => 'true', 'placeholder' => 'Seleccione un área'])  }}
                    <div class="help-block with-errors"></div>
                    @if ($errors->has('id_area'))
                        <div class="alert alert-danger">
                            {{$errors->first('id_area')}}
                        </div>
                    @endif
                </div>
            </div>
             <div class="form-group">
                <label for="id_role" class="col-sm-3 control-label">Rol Asignado</label>
                <div class="col-sm-2">
                    {{ Form::select('id_role', $roles, isset($model->id_role) ? $model->id_role : null , ['class' => 'form-control', 'id' => 'id_role', 'required' => 'true', 'placeholder' => 'Seleccione un rol'])  }}
                    <div class="help-block with-errors"></div>
                    @if ($errors->has('id_role'))
                        <div class="alert alert-danger">
                            {{$errors->first('id_role')}}
                        </div>
                    @endif
                </div>
            </div>
                                                            
            <div class="form-group">
                <div class="col-sm-offset-3 col-sm-6">
                    <a class="btn btn-default"  href="{{ url('/users') }}"><i class="glyphicon glyphicon-chevron-left"></i> Atrás</a>
                    <button type="submit" id="guardar" name="guardar" class="btn btn-success">
                        <i class="fa fa-save"></i> Guardar
                    </button> 

                </div>
            </div>
        </form>

    </div>
</div>






@endsection


@section('scripts')


    <script type="text/javascript" src="/js/views/users.js"></script>


@endsection