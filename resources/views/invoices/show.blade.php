@extends('layout.principal')

@section('content')



<h2 class="page-header">Facturas</h2>
<br>
<div class="panel panel-default">
    <div class="panel-heading">
        <span class="tituloPantalla">Detalle de Factura  </span>
    </div>

    <div class="panel-body">

        <form action="{{ url('/invoices') }}" method="POST" class="form-horizontal">

        <div class="form-group">
            <label for="id_invoice" class="col-sm-3 control-label">N° Factura</label>
            <div class="col-sm-4">
                <input type="text" name="id_invoice" id="id_invoice" class="form-control" value="{{$model['id_document'] or ''}}" readonly="readonly">
            </div>
        </div>
                
        <div class="form-group">
            <label for="id_provider" class="col-sm-3 control-label">Nombre de Proveedor</label>
            <div class="col-sm-5">
                <input type="text" name="id_provider" id="id_provider" class="form-control" value="{{$provider->name_provider or ''}}" readonly="readonly">
            </div>
        </div>

        <div class="form-group">
            <label for="billing_month" class="col-sm-3 control-label">Período de Facturación</label>
            <div class="row">
                <div class="col-sm-1">
                    <input type="text" name="billing_day" id="billing_day" class="form-control" value="{{$model['billing_day'] or ''}}" readonly="readonly">
                </div>
                <div class="col-sm-2">
                    <input type="text" name="billing_month" id="billing_month" class="form-control" value="{{$month->name_month or ''}}" readonly="readonly">
                </div>
                <div class="col-sm-1">
                    <input type="text" name="billing_year" id="billing_year" class="form-control" value="{{$model['billing_year'] or ''}}" readonly="readonly">
                </div>
            </div>
        </div>
                
        <div class="form-group">
            <label for="total" class="col-sm-3 control-label">Monto Total</label>
            <div class="col-sm-4">
                <input type="text" name="total" id="total"  class="form-control" value="{{$currency->short_name or ''}} {{$model['total'] or ''}}" readonly="readonly">
            </div>
        </div>
                
        <div class="form-group">
            <label for="total_iva" class="col-sm-3 control-label">Monto Total con Impuestos</label>
            <div class="col-sm-4">
                <input type="text" name="total_iva" id="total_iva" class="form-control" value="{{$currency->short_name or ''}} {{$model['total_iva'] or ''}}" readonly="readonly">
            </div>
        </div>
        
                
        <div class="form-group">
            <label for="id_currency" class="col-sm-3 control-label">Moneda</label>
            <div class="col-sm-4">
                <input type="text" name="id_currency" id="id_currency" class="form-control" value="{{$currency->name_currency or ''}} - {{$currency->short_name or ''}}" readonly="readonly">
            </div>
        </div>

        <div class="form-group">
            <label for="id_currency" class="col-sm-3 control-label">Área</label>
            <div class="col-sm-4">
                <input type="text" name="id_area" id="id_area" class="form-control" value="{{$area->long_name or 'No tiene área asociada'}}" readonly="readonly">
            </div>
        </div>


        <div class="form-group ">

            <div class="col-sm-2">

            </div>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <span class="tituloPantalla"> Órdenes de Compra Asociadas</span>   </div>

                <div class="panel-body">
                    <div class="form-group">
                        <div class="col-sm-8 col-sm-offset-2">
                            <div id="mensaje_error_detalle" name="mensaje_error_detalle" class="has-error has-danger">
                            </div>
                            <table id="ordenesFactura" class="table table-striped  table-condensed table-framed table-hover full-width " style="align-content: center">
                                <thead>
                                <tr>
                                    <th style="width: 20%">Nro Orden</th>
                                    <th class="text-center" style="width: 20%">Monto Orden</th>
                                    <th class="text-center" style="width: 20%">Monto Facturado</th>
                                </tr>
                                </thead>
                                <tbody>
                                @if(count($orders) > 0)
                                    @foreach($orders as $order)
                                        <tr>
                                            <td>{{$order->folio_number}}</td>
                                            <td class="text-right">{{$order->total}}</td>
                                            <td class="text-right">{{$order->subtotal}}</td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="3" class="text-center">La factura no tiene órdenes asociadas</td>
                                    </tr>
                                @endif

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        
        <div class="form-group">
            <div class="col-sm-offset-3 col-sm-6">
                <a class="btn btn-default" href="{{ url('/invoices') }}"><i class="glyphicon glyphicon-chevron-left"></i> Atrás</a>
            </div>
        </div>

        </form>

    </div>
</div>

@endsection