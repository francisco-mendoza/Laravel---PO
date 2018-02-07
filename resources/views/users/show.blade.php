@extends('layout.principal')

@section('content')



<h2 class="page-header">Usuario</h2>
<br />
<div class="panel panel-default">
    <div class="panel-heading">
        <span class="tituloPantalla">Detalle de Usuario</span>   </div>

    <div class="panel-body">
                

        <form action="{{ url('/users') }}" method="POST" class="form-horizontal">

                


        <div class="form-group">
            <label for="url_avatar" class="col-sm-3 control-label">Imagen de Usuario</label>
            <div class="col-sm-6">
                <img src="{{$model->url_avatar}}" alt="">
            </div>
        </div>

        <div class="form-group">
            <label for="id_user" class="col-sm-3 control-label">Id Usuario</label>
            <div class="col-sm-6">
                <input type="text" name="id_user" id="id_user" class="form-control" value="{{$model->id_user or ''}}" readonly="readonly">
            </div>
        </div>
        
                
        <div class="form-group">
            <label for="username" class="col-sm-3 control-label">Nombre de Usuario</label>
            <div class="col-sm-6">
                <input type="text" name="username" id="username" class="form-control" value="{{$model->username or ''}}" readonly="readonly">
            </div>
        </div>


        <div class="form-group">
            <label for="firstname" class="col-sm-3 control-label">Nombre</label>
            <div class="col-sm-6">
                <input type="text" name="firstname" id="firstname" class="form-control" value="{{$model->firstname or ''}}" readonly="readonly">
            </div>
        </div>


        <div class="form-group">
            <label for="lastname" class="col-sm-3 control-label">Apellido</label>
            <div class="col-sm-6">
                <input type="text" name="lastname" id="lastname" class="form-control" value="{{$model->lastname or ''}}" readonly="readonly">
            </div>
        </div>


        <div class="form-group">
            <label for="email" class="col-sm-3 control-label">Correo</label>
            <div class="col-sm-6">
                <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-envelope-o" ></i></span>
                    <input type="text" name="email" id="email" class="form-control" value="{{$model->email or ''}}" readonly="readonly">
                    </div>
            </div>
        </div>


        <div class="form-group">
            <label for="id_area" class="col-sm-3 control-label">Area Asignada</label>
            <div class="col-sm-6">
                <input type="text" name="id_area" id="id_area" class="form-control" value="{{$model->long_name or 'Area no asignada'}}" readonly="readonly">
            </div>
        </div>


        <div class="form-group">
            <label for="id_role" class="col-sm-3 control-label">Rol Asignado</label>
            <div class="col-sm-6">
                <input type="text" name="id_role" id="id_role" class="form-control" value="{{$model->display_name or 'Rol no asignado'}}" readonly="readonly">
            </div>
        </div>




        
        <div class="form-group">
            <div class="col-sm-offset-3 col-sm-6">
                <a class="btn btn-default" href="{{ url('/users') }}"><i class="glyphicon glyphicon-chevron-left"></i> Atrás</a>
            </div>
        </div>


        </form>

    </div>
</div>







@endsection