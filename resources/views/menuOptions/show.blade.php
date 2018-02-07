@extends('layout.principal')

@section('content')



<h2 class="page-header">Opciones de Menú</h2>
</br>
<div class="panel panel-default">
    <div class="panel-heading">
        Detalle de Opción de Menú    </div>

    <div class="panel-body">
                

        <form action="{{ url('/menuOptions') }}" method="POST" class="form-horizontal">


                
        <div class="form-group">
            <label for="id_menu" class="col-sm-3 control-label">Id Menu</label>
            <div class="col-sm-6">
                <input type="text" name="id_menu" id="id_menu" class="form-control" value="{{$model['id_menu'] or ''}}" readonly="readonly">
            </div>
        </div>
        
                
        <div class="form-group">
            <label for="name_option" class="col-sm-3 control-label">Nombre Opción</label>
            <div class="col-sm-6">
                <input type="text" name="name_option" id="name_option" class="form-control" value="{{$model['name_option'] or ''}}" readonly="readonly">
            </div>
        </div>
        
                
        <div class="form-group">
            <label for="order_option" class="col-sm-3 control-label">Orden</label>
            <div class="col-sm-6">
                <input type="text" name="order_option" id="order_option" class="form-control" value="{{$model['order_option'] or ''}}" readonly="readonly">
            </div>
        </div>
        
                
        <div class="form-group">
            <label for="option_route" class="col-sm-3 control-label">Ruta</label>
            <div class="col-sm-6">
                <input type="text" name="option_route" id="option_route" class="form-control" value="{{$model['option_route'] or ''}}" readonly="readonly">
            </div>
        </div>
        
                
        <div class="form-group">
            <label for="option_icon" class="col-sm-3 control-label">Icono</label>
            <div class="col-sm-6">
                <input type="text" name="option_icon" id="option_icon" class="form-control" value="{{$model['option_icon'] or ''}}" readonly="readonly">
            </div>
        </div>
        
        
        <div class="form-group">
            <div class="col-sm-offset-3 col-sm-6">
                <a class="btn btn-default" href="{{ url('/menuOptions') }}"><i class="glyphicon glyphicon-chevron-left"></i> Atrás</a>
            </div>
        </div>


        </form>

    </div>
</div>







@endsection