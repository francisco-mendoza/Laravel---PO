@extends('layout.principal')

@section('content')

<div class="panel panel-default">
    <div class="panel-heading">
        <span class="tituloPantalla">Nueva Orden de Compra</span>
    </div>
    <div class="panel-body">
        <form id="f_orden" class="form-horizontal" action="{{url('/savePurchaseOrder')}}" method="POST" >
            {{ csrf_field() }}
            <input type="hidden" name="count_detail" id="count_detail" value="0" >
            <input type="hidden" name="ordersByMonth" id="ordersByMonth" value="0" />
        <div class="row ">
            <fieldset>
                <legend>
                    <span class="number ">1</span> Datos del Proveedor
                </legend>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-lg-3 control-label">Razón Social </label>
                        <div class="col-lg-9">
                            <input type="text" name="name_provider" id="name_provider" class="form-control typeahead" placeholder="Seleccione un proveedor"  required maxlength="128" data-containsProvider value="{{$model['value'] or ''}}">
                            <div class="help-block with-errors"></div>
                            @if ($errors->has('name_provider'))
                                <div class="alert alert-danger">
                                    {{$errors->first('name_provider')}}
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label">Dirección </label>
                        <div class="col-lg-9">
                            <input type="text" name="address" id="address" class="form-control " readonly>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group ">
                        <label class="col-lg-3 control-label">RUT </label>
                        <div class="col-lg-9">
                            <input type="text" name="rut" id="rut" class="form-control " readonly>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label">Teléfono </label>
                        <div class="col-lg-9">
                            <input type="text" name="phone" id="phone" class="form-control " readonly>
                        </div>
                    </div>
                </div>
            </fieldset>
            <hr>
        </div>
        <div class="row " >
            <div class="form-inline row">
                <div class="col-md-6">
                    <div class="form-group" style="width: 102%">
                        <label class="col-sm-4 control-label align-center-number-label paso-2"><span class="number ">2</span> CONDICIÓN DE PAGO</label>

                        <div class=" col-sm-8 paso-2 align-div">
                            {{ Form::select('id_payment_conditions', $conditions,  isset($model->id_payment_condition) ? $model->id_payment_condition : null , ['class' => 'form-control paso-2 combo-align-div','placeholder' => 'Seleccione una condición de pago','name'=>'payment_condition', 'id'=>'payment_condition', 'required' => 'true', 'data-selectCondition' => 'true'])  }}
                            <div class="help-block with-errors"></div>
                            @if ($errors->has('payment_condition'))
                                <div class="alert alert-danger">
                                    {{$errors->first('payment_condition')}}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group" style="width: 100%">
                        <label for="payment-method" class="col-sm-4 control-label align-center-number-label paso-3"><span class="number ">3</span> MÉTODO DE PAGO</label>
                        <div class="col-sm-8 paso-3 align-div">
                            {{ Form::select('id_payment_method', $methods,  isset($model->id_payment_method) ? $model->id_payment_method : null , ['class' => 'form-control paso-3 combo-align-div','placeholder' => 'Seleccione un método de pago','name'=>'payment_method', 'id'=>'payment_method', 'required' => 'true', 'data-selectMethod' => 'true'])  }}
                            <div class="help-block with-errors"></div>
                            @if ($errors->has('payment_method'))
                                <div class="alert alert-danger">
                                    {{$errors->first('payment_method')}}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <hr>
        </div>

        <div class="row paso-4 ">
            <fieldset>
                <legend class="paso-4">
                    <span class="number ">4</span> Producto a Comprar
                </legend>
                <div class="col-sm-5">
                    <div class="form-group">
                        <label class="col-lg-3 control-label">Moneda </label>
                        <div class="col-lg-7">
                           {{ Form::select('id_currency',$currencies, isset($model->id_currency) ? $model->id_currency : null , ['class' => 'form-control paso-4','placeholder' => 'Seleccione una moneda','name'=>'currency', 'id'=>'currency', 'required' => 'true','data-selectCurrency' => 'true'])  }}
                            <div class="help-block with-errors"></div>
                            @if ($errors->has('currency'))
                                <div class="alert alert-danger">
                                    {{$errors->first('currency')}}
                                </div>
                            @endif
                        </div>


                    </div>
                </div>

                <div class="col-sm-6">
                    {{--<label class="col-lg-3 control-label">Boleta/Factura </label>--}}
                    <label class="radio-inline">
                        <div class="choice border-info-600 text-info-800" style="top: 0">
                            <input type="radio" value="1" name="tipo_boleta" class="styled paso-5" checked="checked">
                        </div>
                        Boleta/Factura

                    </label>
                    <label class="radio-inline">
                        <div class="choice border-info-600 text-info-800" style="top: 0">
                            <input type="radio" value="2" name="tipo_boleta" class="styled paso-5">
                        </div> Boleta Honorarios
                    </label>

                </div>
                <div class="col-sm-12">
                    <div id="mensaje_error_detalle" name="mensaje_error_detalle" class="has-error has-danger">

                    </div>
                    <table class="table">
                        <thead>
                            <tr>
                                <th width="110px;">Cantidad</th>
                                <th>Descripción</th>
                                <th width="245px;"  >
                                    <div data-toggle="tooltip" title="Mes de finalización (opcional)">Mes (Inicio / Fin)</div>
                                </th>
                                <th id="importe_primero">Importe Unitario por mes s/IVA</th>
                                <th id="tipo_impuesto" width="50px;">IVA</th>
                                <th id="importe_segundo">Importe Unitario por mes c/IVA</th>
                            </tr>
                        </thead>
                        <tbody id="body_product_oc">
                        </tbody>
                    </table>
                </div>
                <div class="row col-sm-4">
                    <button type="button" id="add_product" class="btn btn-primary paso-5"><i class="fa fa-plus-circle fa-lg" aria-hidden="true"></i></button>
                </div>
                <br/>
                <div class="row col-sm-12">
                    <div class="form-group">
                        <div class="col-sm-offset-9 col-sm-1" >
                            <label for="total_sin_iva" id="lbl_total_sin_iva">Total Sin Iva</label>
                        </div>
                        <div class="col-sm-2">
                            <input type="text" class="form-control col-sm-2 money" readonly name="total_sin_iva" id="total_sin_iva"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-offset-9 col-sm-1" >
                            <label for="total" id="lbl_total_con_iva">Total Con Iva</label>
                        </div>
                        <div class="col-sm-2">
                            <input type="text" class="form-control money" readonly name="total" id="total"/>
                        </div>
                    </div>
                </div>
            </fieldset>
        </div>
            <br />
        <div class="form-group">
            <div class="col-sm-offset-9 col-sm-3">
                <a class="btn btn-default" href="{{ url('/consultarOrdenes') }}"><i class="glyphicon glyphicon-chevron-left"></i> Atrás</a>
                <button id="guardar" name="guardar" type="submit" class="btn btn-success ">
                    <i class="fa fa-cogs"></i> Generar Orden de Compra
                </button>

            </div>
        </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
    <script>let id_area = '<?php echo Auth::user()->id_area; ?>'</script>
    {!!Html::script('js/views/purchaseOrderAccions.js')!!}
    <script type="text/javascript" src="js/views/createPurchaseOrder.js"> </script>
@endsection