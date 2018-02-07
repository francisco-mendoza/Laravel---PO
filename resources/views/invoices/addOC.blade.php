@extends('layout.principal')

@section('content')


    <h4 class="page-header">Agregar/Editar Asignación de Órdenes de Compra</h4>
    <br>
    <div class="panel panel-flat border-left-xlg border-left-info">
        <div class="panel-body ">
            <h5>Datos de la Factura</h5>
            <fieldset>
                <div class="form-inline row">
                    <div class="col-sm-4">
                        <table class="full-width">
                            <tbody>
                            <tr>
                                <td class="text-center">
                                    <label class="control-label text-left" for="invoice"><span class="text-semibold">N° Factura:</span></label>
                                    <label id="invoice" class="control-label text-center" >{{$invoice->id_document or ""}} </label>
                                </td>

                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-sm-4">
                        <table class="full-width">
                            <tbody>
                                <tr>
                                    <td class="text-center">
                                        <label class="  control-label " for="provider_bill"><span class="text-semibold">Proveedor:</span></label>
                                        <label id="provider_bill" class="control-label text-center">{{$provider->name_provider or ""}}</label>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-sm-4">
                        <table class="full-width">
                            <tbody>
                                <tr>
                                    <td class="text-center" id="total_bill_group">
                                        <label class=" control-label " for="total_bill"><span class="text-semibold">Total:</span></label>
                                        <label id="total_bill" class="control-label  ">{{$invoice->total ." ". $currency->short_name ." " }} <i  class="fa fa-exclamation fa-lg text-warning-800" id="icon_total_bill" style="display: none"></i></label>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                </div>
            </fieldset>
            <br>



        </div>
    </div>
    <div class="panel panel-flat border-left-xlg border-left-info">
        <div class="panel-body">
            <form class="steps-basic " action="{{ url('/invoices/assignOC/'.( isset($invoice) ? $invoice->id_invoice : "")) }}" method="POST" id="f_invoice" name="f_invoice" >
                {{ csrf_field() }}


                <h6>Selección de Órdenes</h6>
                <fieldset>


                    <div class="row">
                        @if($count>0)
                            <div class="col-md-12">
                                <div class="form-group">
                                    <h3>Órdenes previamente asociadas a la Factura:
                                        <small class="display-block">Si desea cambiar los valores de cada Orden presione siguiente </small></h3>
                                    <br>
                                    <div class="col-md-10 col-md-offset-1">
                                        <table class="display table table-framed table-hover full-width" cellspacing="0" id="assignated_oc" >
                                            <thead>
                                            <tr><th colspan="5"><small>Mostrando Órdenes de Compra <strong style="color:#0f95a7">previamente asignadas</strong></small></th></tr>
                                            <tr>
                                                <th>Nro. Orden</th>
                                                <th class="center-column"> Pendiente Factura OC</th>
                                                <th class="center-column"> Monto Facturado</th>
                                                <th  class="center-column">Tasa de Cambio </th>
                                                <th  class="center-column">Subtotal Calculado </th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($orders as $order)
                                                <tr>
                                                    <td>{{$order->id_purchase_order}}</td>
                                                    <td class="right-column">{{$order->disp}}</td>
                                                    <td class="right-column">{{$order->subtotal. ' '.$currency->short_name}}</td>
                                                    <td class="center-column">{{$order->exchange_rate}}</td>
                                                    <td class="right-column">{{$order->subtotal_po_currency}}</td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                        <br >
                                    </div>
                                </div>
                                <div class="col-md-12"><a id="activarFiltro" class="btn btn-primary">Agregar Más Órdenes </a></div>
                            </div>


                        @endif
                        <div id="addOC_div" class="col-md-12" hidden>
                            <div class="form-group">

                                <h3>Seleccione las Ordenes de Compra a asociar a la Factura:
                                <small class="display-block">Busque por proveedor y filtre por rango de meses (opcional): </small></h3>
                                <br>
                                <div id="message_error_selection" name="message_error_selection" class="has-error has-danger">
                                </div>
                                <fieldset >
                                    <div class="form-inline row">
                                        <table >
                                            <tbody>
                                            <tr>
                                                <td class="col-sm-1">
                                                    <label class="  control-label" for="provider">Proveedor:</label>
                                                </td>
                                                <td class="col-sm-4">
                                                    <div >
                                                        <input type="text" id="provider" name="provider" class=" form-control " placeholder="Ejemplo: AMAZON" style="width:100% !important;">
                                                    </div>

                                                </td>
                                                <td class="col-sm-1 none-padding">
                                                    <label class="  control-label label-align-right" for="monthIni">Mes Inicial:</label>
                                                </td>
                                                <td class="col-sm-2">
                                                    <input type="text" id="monthIni" name="monthIni" class=" form-control filter" maxlength="2" data-column-index='5' placeholder="Ejemplo: 05">
                                                </td>
                                                <td class="col-sm-1 none-padding">
                                                    <label  class=" control-label label-align-right" for="monthEnd">Mes Final:</label>
                                                </td>
                                                <td class="col-sm-2">
                                                    <input type="text" id="monthEnd" name="monthEnd" class="form-control filter" maxlength="2" data-column-index='5' placeholder="Ejemplo: 07">
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>

                                    </div>
                                </fieldset>
                                <br>
                                <table class="display table table-framed table-hover full-width" cellspacing="0" id="billingOrdersGrid" >
                                    <thead>
                                    <tr><th colspan="6"><small>Filtrando Órdenes de Compra <strong style="color:#0f95a7">Aprobadas</strong></small></th></tr>
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th>Nro. Orden</th>
                                        <th style="width:40%" class="center-column">Proveedor</th>
                                        <th style="width:90px" class="center-column"> Importe</th>
                                        <th  class="center-column">Pendiente Factura </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </fieldset>

                <h6>Distribución Monto</h6>
                <fieldset>
                    <div class="row form-horizontal">
                        <h4>Asigne el monto correspondiente a cada Orden de Compra seleccionada:</h4>
                        <br>
                        <div class="col-sm-12">
                            <div id="mensaje_error_detalle" name="mensaje_error_detalle" class="has-error has-danger">
                            </div>
                            <div class="col-md-10 col-md-offset-1  ">
                                <div class="form-group">
                                    <br>
                                    <table class=" table table-framed table-hover full-width" cellspacing="0" id="selectedOrdersGrid" >
                                        <thead>
                                        <tr><th colspan="5"><small>Órdenes de Compra <strong style="color:#0f95a7"> previamente seleccionadas</strong></small></th></tr>
                                        <tr>
                                            <th >Nro. Orden</th>
                                            <th id="pending_to_bill" class="center-column"> Pendiente Factura OC</th>
                                            <th id="column_subtotal" class="center-column"> Subtotal Factura</th>
                                            <th id="column_rate"  class="center-column"> Tasa de Cambio</th>
                                            <th id="column_calculated" class="center-column"> Subtotal Calculado</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </fieldset>

            </form>
        </div>
    </div>

@endsection

@section('scripts')
    <script type="text/javascript" >
        var consulta ="{{url('/purchaseOrder/filterToBill')}}";
        var detail ="{{url('/detailPurchaseOrder') }}";
        var orders = {!! $orders !!};
        var id_currency = "{{ $invoice->id_currency }}";
        var invoice_date = "{{ $invoice->billing_year.'-'.$invoice->billing_month.'-'.$invoice->billing_day }}";
    </script>

    <script type="text/javascript" src="/js/views/add_oc_invoices.js"></script>
    <script type="text/javascript" src="/assets/template/js/plugins/forms/validation/validate.min.js"></script>

@endsection