@extends('layout.principal')

@section('content')


<h2 class="page-header">Método de Pago</h2>
<br />
<div class="panel panel-default">
    <div class="panel-heading">
        <span class="tituloPantalla">Agregar/Editar Método de Pago </span>   </div>

    <div class="panel-body">
                
        <form id="f_methods" name="f_methods" action="{{ url('/paymentmethods'.( isset($model) ? "/" . $model->id_payment_method : "")) }}" method="POST" class="form-horizontal">
            {{ csrf_field() }}

            @if (isset($model))
                <input type="hidden" name="_method" value="PATCH">
                <input type="hidden" name="id_payment_method" id="id_payment_method" value="{{$model->id_payment_method}}">
            @endif

            <div class="form-group">
                <label for="name_method" class="col-sm-3 control-label">Método de Pago</label>
                <div class="col-sm-6">
                    <input type="text" name="name_method" id="name_method" class="form-control" value="{{$model['name_method'] or ''}}" required maxlength="128">
                    <div class="help-block with-errors"></div>
                    @if($errors->has('name_method'))
                        <span class="text-danger">{{ $errors->first('name_method') }}</span>
                    @endif
                </div>
            </div>
                                                            
            <div class="form-group">
                <div class="col-sm-offset-3 col-sm-6">
                    <a class="btn btn-default" href="{{ url('/paymentmethods') }}"><i class="glyphicon glyphicon-chevron-left"></i> Atrás</a>
                    <button id="guardar" name="guardar" type="submit" class="btn btn-success">
                        <i class="fa fa-save"></i> Guardar
                    </button> 

                </div>
            </div>
        </form>

    </div>
</div>






@endsection

@section('scripts')

    <script type="text/javascript" src="/js/views/paymentMethod.js"></script>

@endsection