@extends('layout.principal')

@section('content')



<h2 class="page-header">Condición de Pago</h2>
<br />
<div class="panel panel-default">
    <div class="panel-heading">
        <span class="tituloPantalla">Detalle de Condición de Pago </span>   </div>

    <div class="panel-body">
                

        <form action="{{ url('/paymentconditions') }}" method="POST" class="form-horizontal">


                
        <div class="form-group">
            <label for="id_payment_conditions" class="col-sm-3 control-label">Id de Condición de Pago</label>
            <div class="col-sm-6">
                <input type="text" name="id_payment_conditions" id="id_payment_conditions" class="form-control" value="{{$model['id_payment_conditions'] or ''}}" readonly="readonly">
            </div>
        </div>
        
                
        <div class="form-group">
            <label for="name_condition" class="col-sm-3 control-label">Condición de Pago</label>
            <div class="col-sm-6">
                <input type="text" name="name_condition" id="name_condition" class="form-control" value="{{$model['name_condition'] or ''}}" readonly="readonly">
            </div>
        </div>
        
        
        <div class="form-group">
            <div class="col-sm-offset-3 col-sm-6">
                <a class="btn btn-default" href="{{ url('/paymentconditions') }}"><i class="glyphicon glyphicon-chevron-left"></i> Atrás</a>
            </div>
        </div>


        </form>

    </div>
</div>







@endsection