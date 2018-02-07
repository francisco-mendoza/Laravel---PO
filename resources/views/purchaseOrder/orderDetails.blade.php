@extends('layout.principal')

@section('content')
    <div class="row align-buttons" id="botonesAprobacion" hidden>
        <div class="">
            <a class="btn btn-default" href="{{ url('/aprobarOrdenes') }}"><i class="glyphicon glyphicon-chevron-left"></i> Atrás</a>
            @if($order->order_state == config('constants.emitida'))
                <button id="aprobar" name="aprobar" type="submit" class="btn btn-success float-right">
                    <i class="fa fa-thumbs-up fa-lg"></i> Aprobar
                </button>
                <button id="rechazar" name="rechazar" type="submit" class="btn btn-danger float-right" style="margin-right: 5px;">
                    <i class="fa fa-thumbs-down fa-lg"></i> Rechazar
                </button>
            @endif
        </div>
    </div>
    <div class="row" id="botonesConsulta">
        <div class="col-sm-1">
            @if(Session::get('opcion_consulta_OC') != null)
                <a class="btn btn-default" href="{{ url('/'.Session::get('opcion_consulta_OC')) }}"><i class="glyphicon glyphicon-chevron-left"></i> Atrás</a>
            @else
                <a class="btn btn-default" href="{{ url('/consultarOrdenes') }}"><i class="glyphicon glyphicon-chevron-left"></i> Atrás</a>
            @endif

        </div>
    </div>
    <hr>
    @if($order->order_state == config('constants.rechazada'))
        <div class="alert alert-warning" role="alert"><p><i class="fa fa-exclamation-circle fa-lg" aria-hidden="true"></i>   La orden de compra fue <b>rechazada y no puede ser modificada.</b> Si desea reintentar deberá crear una nueva.</p></div>
        <hr>
    @elseif($order->order_state == config('constants.aprobada'))
        <div class="alert alert-success" role="alert"><p><i class="fa fa-check-circle fa-lg" aria-hidden="true"></i>   La orden de compra fue <b>aprobada.</b> </p></div>
        <hr>
    @endif

    <div class="row" id="ordenImprimir" >

        <table id="tablaImprimir" style="width: 100%;table-layout: fixed;font-family: 'Roboto', Helvetica Neue, Helvetica, Arial, sans-serif; font-size: 13px;">
            <tr>
                <th style="width: 25%;"></th>
                <th style="width: 25%;"></th>
                <th style="width: 25%;"></th>
                <th style="width: 25%;"></th>
            </tr>
            <tr>
                <td colspan="3"><b>Yapo.cl SpA</b></td>
                <td style="max-width: 25%" rowspan="5" >
                    {{ Form::image("images/home_yapo_logo.png", "Logo", array('style' => 'width:100%')) }}
                </td>
            </tr>
            <tr><td colspan="3">Mariano Sánchez Fontecilla #310, of 1001</td></tr>
            <tr><td colspan="3">Las Condes, Santiago de Chile -RM</td></tr>
            <tr><td colspan="3">Giro: Comercio electrónico y publicidad</td></tr>
            <tr><td colspan="3">RUT: 16.169.003-5</td></tr>
            <tr><td colspan="3">Teléfono: +56-2-29519287</td></tr>
            <tr><td colspan="4"><br /></td></tr>
            <tr>
                <td colspan="3"></td>
                <td style="font-size: 14px; font-weight: bold">Orden de Compra</td>
            </tr>
            <tr>
                <td colspan="3"></td>
                <td><b>Folio: </b><span id="folio_number">{{$id}}</span></td>
            </tr>
            <tr><td colspan="4"><br /></td></tr>
            <tr>
                <td colspan="2" style="font-size: 14px; font-weight: bold">Proveedor:</td>
                <td colspan="2" style="font-size: 14px; font-weight: bold">Persona de Contacto:</td>
            </tr>
            <tr><td colspan="4"><br /></td></tr>
            <tr>
                <td><b>Razón Social</b></td>
                <td>{{$provider->name_provider}}</td>
                <td><b>Nombre y Apellidos</b></td>
                <td>{{$provider->contact_name}}</td>
            </tr>
            <tr>
                <td><b>N° Contrato</b></td>
                <td>{{$contract->contract_number}}</td>
                <td><b>Cargo/Área</b></td>
                <td>{{$provider->contact_area}}</td>
            </tr>
            <tr>
                <td><b>RUT</b></td>
                <td>{{$provider->rut}}</td>
                <td><b>Teléfono del contacto</b></td>
                <td>{{$provider->contact_phone}}</td>
            </tr>
            <tr>
                <td><b>Dirección</b></td>
                <td>{{$provider->address}}</td>
                <td><b>Email del contacto</b></td>
                <td>{{$provider->contact_email}}</td>
            </tr>

            <tr>
                <td><b>Teléfono</b></td>
                <td colspan="3">{{$provider->phone}}</td>
            </tr>
            <tr>
                <td><b>Condición de Pago</b></td>
                <td colspan="3">{{$condition->name_condition}}</td>
            </tr>
            <tr>
                <td><b>Método de Pago</b></td>
                <td colspan="3">{{$method->name_method}}</td>
            </tr>
            <tr><td colspan="4"><br /></td></tr>
            <tr><td colspan="4"><br /></td></tr>
            <tr>
                <td colspan="4" >
                    <table class="table" style="width: 100%;font-family: 'Roboto', Helvetica Neue, Helvetica, Arial, sans-serif; font-size: 12px;">
                        <tr>
                            <td class="center-column"><b>Cantidad</b></td>
                            <td><b>Descripción</b></td>
                            <?php $paid_type = $order->paid_type == 1 ? "IVA":"Honorario" ?>
                            <td class="center-column"><b>Importe Unitario Sin {{$paid_type}}</b></td>
                            <td class="center-column"><b>Importe Unitario Con {{$paid_type}}</b></td>
                        </tr>
                        @foreach($details as $item)
                            <tr>
                                <td class="center-column">{{$item->quantity}}</td>
                                <td>{{$item->description}}</td>
                                <td class="right-column">{{ number_format($item->price,2,",",".") }}</td>
                                <td class="right-column">
                                    @if ($item->has_iva == 1)
                                        {{ number_format($item->price_iva,2,",",".") }}
                                    @else
                                        {{number_format($item->price,2,",",".")}}
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </table>

                </td>
            </tr>
            <tr><td colspan="4"><br /></td></tr>
            <tr>
                <td></td>
                <td></td>
                <td><b>Moneda</b></td>
                <td>{{$currency->name_currency}}</td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td><b>Total Sin IVA</b></td>
                <td>{{ number_format($order->total_price,2,",",".")}}</td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td><b>Total Con IVA</b></td>
                <td>@if ($order->total_iva_price != null && $order->total_iva_price!= 0)
                        {{number_format($order->total_iva_price,2,",",".")}}
                    @else
                        {{number_format($order->total_price,2,",",".")}}
                    @endif</td>
            </tr>
            <tr><td colspan="4"><br /><br /><br /><br /><br /><br /></td></tr>
            <tr><td colspan="4"><br /></td></tr>


        </table>

    </div>
    <div class="row" >
        <table id="footer" style="width: 100%;table-layout: fixed;">
            <tr>
                <th style="width: 25%;"></th>
                <th style="width: 25%;"></th>
                <th style="width: 25%;"></th>
                <th style="width: 25%;"></th>
            </tr>
            <tr>
                <td colspan="4"><b>Fecha: </b>{{$order->date_purchase}}</td>
            </tr>
            <tr>
                <td colspan="2"><b>Encargado: </b>{{$area->manager_name}}</td>
                <td class="center-column">_________________________________</td>
                <td></td>
            </tr>
            <tr>
                <td colspan="2"><b>Cargo: </b> {{$area->manager_position}}</td>
                <td class="center-column">Firma</td>
                <td></td>
            </tr>
            <tr>
                <td colspan="4"><b>Área: </b>{{$area->long_name}}</td>

            </tr>
        </table>
    </div>






@endsection

@section('scripts')
    <script type="text/javascript" >
        var action ="{{$action}}";

        var idOrder ="{{$id}}";

        var listadoAprobar = "{{url('/aprobarOrdenes')}}";

    </script>

    <script type="text/javascript" src="/js/views/detailPurchaseOrder.js"> </script>


@endsection