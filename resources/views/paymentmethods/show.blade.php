@extends('layout.principal')

@section('content')


    <h2 class="page-header" xmlns="http://www.w3.org/1999/html">Método de Pago</h2>
<br />
<div class="panel panel-default">
    <div class="panel-heading">
        <span class="tituloPantalla">Detalle de Método de Pago</span>   </div>

    <div class="panel-body">
                

        <form action="{{ url('/paymentmethods') }}" method="POST" class="form-horizontal">


                
        <div class="form-group">
            <label for="id_payment_method" class="col-sm-3 control-label">Id de Método de Pago</label>
            <div class="col-sm-6">
                <input type="text" name="id_payment_method" id="id_payment_method" class="form-control" value="{{$model['id_payment_method'] or ''}}" readonly="readonly">
            </div>
        </div>
        
                
        <div class="form-group">
            <label for="name_method" class="col-sm-3 control-label">Método de Pago</label>
            <div class="col-sm-6">
                <input type="text" name="name_method" id="name_method" class="form-control" value="{{$model['name_method'] or ''}}" readonly="readonly">
            </div>
        </div>
        
        
        <div class="form-group">
            <div class="col-sm-offset-3 col-sm-6">
                <a class="btn btn-default" href="{{ url('/paymentmethods') }}"><i class="glyphicon glyphicon-chevron-left"></i> Atrás</a>
            </div>
        </div>


        </form>

    </div>
</div>







@endsection