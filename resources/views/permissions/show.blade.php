@extends('layout.principal')

@section('content')



<h2 class="page-header">Permisos</h2>
<br />
<div class="panel panel-default">
    <div class="panel-heading">
        <span class="tituloPantalla">Detalle de Permiso</span>   </div>

    <div class="panel-body">
                

        <form action="{{ url('/permissions') }}" method="POST" class="form-horizontal">


                
        <div class="form-group">
            <label for="id_role" class="col-sm-3 control-label">Id Permiso</label>
            <div class="col-sm-6">
                <input type="text" name="id_permission" id="id_permission" class="form-control" value="{{$model['id'] or ''}}" readonly="readonly">

            </div>
        </div>

                
        <div class="form-group">
            <label for="description" class="col-sm-3 control-label">Nombre</label>
            <div class="col-sm-6">

                <input type="text" name="name" id="name" class="form-control" value="{{$model->name or ''}}" readonly="readonly">
                <span></span>
            </div>
        </div>

            <div class="form-group">
                <label for="description" class="col-sm-3 control-label">Nombre a mostrar </label>
                <div class="col-sm-6">

                    <input type="text" name="name" id="name" class="form-control" value="{{$model->display_name or ''}}" readonly="readonly">
                    <span></span>
                </div>
            </div>

        <hr>

        <div class="form-group">
            <div class="col-sm-offset-3 col-sm-6">
                <a class="btn btn-default" href="{{ url('/permissions') }}"><i class="glyphicon glyphicon-chevron-left"></i> Atrás</a>
            </div>
        </div>


        </form>

    </div>
</div>







@endsection