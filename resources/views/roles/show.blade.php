@extends('layout.principal')

@section('content')



<h2 class="page-header">Roles</h2>
<br />
<div class="panel panel-default">
    <div class="panel-heading">
        <span class="tituloPantalla">Detalle de Rol</span>   </div>

    <div class="panel-body">
                

        <form action="{{ url('/roles') }}" method="POST" class="form-horizontal">


                
        <div class="form-group">
            <label for="id_role" class="col-sm-3 control-label">Id Rol</label>
            <div class="col-sm-6">
                <input type="text" name="id_role" id="id_role" class="form-control" value="{{$model['id'] or ''}}" readonly="readonly">

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

        <div class="form-group ">
            <label for="is_default" class="col-sm-3 control-label">Es rol por defecto?</label>
            <div class="col-sm-6 checker border-info-600 text-info-800">{{ Form::checkbox('is_default', $model->id_role,  $model->is_default,['readonly'=>'readonly', 'disabled'=>'disabled', 'class' => 'styled ']) }}</div>
        </div>

        <hr>
        <span class=" tituloPantalla">Permisos de Rol</span>
        <div class="form-group">

            <br />
            <div  style="overflow-y: scroll; height:300px;">
            @foreach($optionsRole as $permiso)
                @php
                    if(isset($optionsRole)){
                                $is_checked = $optionsRole->contains('id_permiso', $permiso->id_permiso);
                            }else{
                                $is_checked = '';
                            }
                @endphp


                <ul id="lista-permisos" class="list-group list-group-hover list-group-striped">
                    <a class="list-group-item"><i class="fa fa-check" aria-hidden="true"></i> {{$permiso->description}}</a>
                </ul>



            @endforeach
            </div>
        </div>
        
        <div class="form-group">
            <div class="col-sm-offset-3 col-sm-6">
                <a class="btn btn-default" href="{{ url('/roles') }}"><i class="glyphicon glyphicon-chevron-left"></i> Atrás</a>
            </div>
        </div>


        </form>

    </div>
</div>







@endsection