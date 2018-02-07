@extends('layout.principal')

@section('content')



<h2 class="page-header">Moneda</h2>
<br>
<div class="panel panel-default">
    <div class="panel-heading">
        <span class="tituloPantalla">Detalle Moneda</span>
    </div>

    <div class="panel-body">
                

        <form action="{{ url('/currencies') }}" method="POST" class="form-horizontal">


                
        <div class="form-group">
            <label for="id_currency" class="col-sm-3 control-label">Id Moneda</label>
            <div class="col-sm-6">
                <input type="text" name="id_currency" id="id_currency" class="form-control" value="{{$model['id_currency'] or ''}}" readonly="readonly">
            </div>
        </div>
        
                
        <div class="form-group">
            <label for="name_currency" class="col-sm-3 control-label">Nombre Moneda</label>
            <div class="col-sm-6">
                <input type="text" name="name_currency" id="name_currency" class="form-control" value="{{$model['name_currency'] or ''}}" readonly="readonly">
            </div>
        </div>
        
                
        <div class="form-group">
            <label for="short_name" class="col-sm-3 control-label">Sigla</label>
            <div class="col-sm-6">
                <input type="text" name="short_name" id="short_name" class="form-control" value="{{$model['short_name'] or ''}}" readonly="readonly">
            </div>
        </div>

        <div class="form-group">
            <label for="code" class="col-sm-3 control-label">Código</label>
            <div class="col-sm-6">
                <input type="text" name="code" id="code" class="form-control" value="{{$model['code'] or ''}}" readonly="readonly">
            </div>
        </div>


        <div class="form-group">
            <div class="col-sm-offset-3 col-sm-6">
                <a class="btn btn-default" href="{{ url('/currencies') }}"><i class="glyphicon glyphicon-chevron-left"></i> Atrás</a>
            </div>
        </div>


        </form>

    </div>
</div>







@endsection