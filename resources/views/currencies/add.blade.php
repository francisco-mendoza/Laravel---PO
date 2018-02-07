@extends('layout.principal')

@section('content')


<h2 class="page-header">Monedas</h2>
<br>
<div class="panel panel-default">
    <div class="panel-heading">
        <span>Agregar/Editar Moneda</span>
    </div>

    <div class="panel-body">
                
        <form action="{{ url('/currencies'.( isset($model) ? "/" . $model->id_currency : "")) }}" method="POST" class="form-horizontal">
            {{ csrf_field() }}

            @if (isset($model))
                <input type="hidden" name="_method" value="PATCH">
                <input type="hidden" name="id_currency" id="id_currency" class="form-control" value="{{$model['id_currency']}}">
            @endif


            <div class="form-group">
                <label for="name_currency" class="col-sm-3 control-label">Nombre Moneda</label>
                <div class="col-sm-6">
                    <input type="text" name="name_currency" id="name_currency" class="form-control" value="{{$model['name_currency'] or ''}}">
                    @if($errors->has('name_currency'))
                        <span class="text-danger">{{ $errors->first('name_currency') }}</span>
                    @endif
                </div>
            </div>
            <div class="form-group">
                <label for="short_name" class="col-sm-3 control-label">Sigla</label>
                <div class="col-sm-6">
                    <input type="text" name="short_name" id="short_name" class="form-control" value="{{$model['short_name'] or ''}}">
                    @if($errors->has('short_name'))
                        <span class="text-danger">{{ $errors->first('short_name') }}</span>
                    @endif
                </div>
            </div>
            <div class="form-group">
                <label for="code" class="col-sm-3 control-label">Código</label>
                <div class="col-sm-6">
                    <input type="text" name="code" id="code" class="form-control" value="{{$model['code'] or ''}}">
                    @if($errors->has('code'))
                        <span class="text-danger">{{ $errors->first('code') }}</span>
                    @endif
                </div>
            </div>
                                                            
            <div class="form-group">
                <div class="col-sm-offset-3 col-sm-6">
                    <a class="btn btn-default" href="{{ url('/currencies') }}"><i class="glyphicon glyphicon-chevron-left"></i> Atrás</a>
                    <button type="submit" class="btn btn-success">
                        <i class="fa fa-save"></i> Guardar
                    </button> 

                </div>
            </div>
        </form>

    </div>
</div>






@endsection