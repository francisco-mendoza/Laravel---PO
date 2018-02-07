@extends('layout.principal')

@section('content')


<h2 class="page-header">Condiciones de Pago</h2>
<br />
<div class="panel panel-default">
    <div class="panel-heading">
        <span class="tituloPantalla">Agregar/Editar Condición de Pago </span> </div>

    <div class="panel-body">
                
        <form name="f_conditions" id="f_conditions" action="{{ url('/paymentconditions'.( isset($model) ? "/" . $model->id_payment_conditions : "")) }}" method="POST" class="form-horizontal">
            {{ csrf_field() }}

            @if (isset($model))
                <input type="hidden" name="_method" value="PATCH">
                <input type="hidden" name="id_payment_conditions" id="id_payment_conditions" value="{{$model['id_payment_conditions']}}">
            @endif

            <div class="form-group">
                <label for="name_condition" class="col-sm-3 control-label">Condición de Pago</label>
                <div class="col-sm-6">
                    <input type="text" name="name_condition" id="name_condition" class="form-control" value="{{$model['name_condition'] or ''}}" required maxlength="128">
                    <div class="help-block with-errors"></div>
                    @if(isset($errors) && $errors->has('name_condition'))
                        <span class="text-danger">{{ $errors->first('name_condition') }}</span>
                    @endif
                </div>
            </div>
                                                            
            <div class="form-group">
                <div class="col-sm-offset-3 col-sm-6">
                    <a class="btn btn-default" href="{{ url('/paymentconditions') }}"><i class="glyphicon glyphicon-chevron-left"></i> Atrás</a>
                    <button name="guardar" id="guardar" type="submit" class="btn btn-success">
                        <i class="fa fa-save"></i> Guardar
                    </button> 

                </div>
            </div>
        </form>

    </div>
</div>






@endsection

@section('scripts')
    <script type="text/javascript" src="/js/views/paymentCondition.js"></script>

@endsection