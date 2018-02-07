@extends('layout.principal')

@section('content')

    <div class="panel panel-default">
        <div class="panel-heading">
            <span class="tituloPantalla">Editar Orden de Compra</span>
        </div>
        <div class="panel-body">
            <form id="f_orden" class="form-horizontal" action="{{url('/updatePurchaseOrder')}}" method="POST" >
                {{ csrf_field() }}
                <input type="hidden" name="count_detail" id="count_detail" value="0" >
                <input type="hidden" name="ordersByMonth" id="ordersByMonth" value="0" />
                <input type="hidden" name="items" id="items" value="0" />
                <input type="hidden" name="purchase_order" id="purchase_order" value="{{$order->folio_number}}" />
                <input type="hidden" name="first_item_description" id="first_item_description" value="{{$details[0]->description}}" />
                <div class="row ">
                    <fieldset>
                        <legend>
                            <span class="number ">1</span> Datos del Proveedor
                        </legend>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="col-lg-3 control-label">Razón Social </label>
                                <div class="col-lg-9">
                                    <input type="text" name="name_provider" id="name_provider" value="{{$provider->name_provider." - ".$contract->contract_number." (".$contract->description.")"}}" readonly class="form-control typeahead" placeholder="Seleccione un proveedor"  required maxlength="128" data-containsProvider value="{{$model['value'] or ''}}">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-3 control-label">Dirección </label>
                                <div class="col-lg-9">
                                    <input type="text" name="address" id="address" class="form-control " value="{{$provider->address}}" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group ">
                                <label class="col-lg-3 control-label">RUT </label>
                                <div class="col-lg-9">
                                    <input type="text" name="rut" value="{{$provider->rut}}" id="rut" class="form-control " readonly>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-3 control-label">Teléfono </label>
                                <div class="col-lg-9">
                                    <input type="text" name="phone" id="phone" class="form-control " value="{{$provider->phone}}" readonly>
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
{{--                                    {{ Form::select('id_payment_conditions', $conditions,  isset($model->id_payment_condition) ? $model->id_payment_condition : null , ['class' => 'form-control paso-2 combo-align-div','placeholder' => 'Seleccione una condición de pago','name'=>'payment_condition', 'id'=>'payment_condition', 'required' => 'true', 'data-selectCondition' => 'true'])  }}--}}
                                    <select class='form-control combo-align-div' readonly><option selected readonly>{{$condition->name_condition}}</option></select>

                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group" style="width: 100%">
                                <label for="payment-method" class="col-sm-4 control-label align-center-number-label"><span class="number ">3</span> MÉTODO DE PAGO</label>
                                <div class="col-sm-8 paso-3 align-div">
{{--                                    {{ Form::select('id_payment_method', $methods,  isset($model->id_payment_method) ? $model->id_payment_method : null , ['class' => 'form-control paso-3 combo-align-div','placeholder' => 'Seleccione un método de pago','name'=>'payment_method', 'id'=>'payment_method', 'required' => 'true', 'data-selectMethod' => 'true'])  }}--}}
                                    <select class='form-control paso-3 combo-align-div' readonly><option selected readonly>{{$method->name_method}}</option></select>

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
                                    <select class='form-control' readonly id="currency">
                                        <option selected readonly value="{{$currency->id_currency}}">{{$currency->name_currency}}</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <label class="radio-inline">
                                <div class="choice border-info-600 text-info-800" style="top: 0">
                                    <input type="radio" value="1" name="tipo_boleta" class="styled" checked="checked" disabled>
                                </div> Boleta/Factura
                            </label>
                            <label class="radio-inline">
                                <div class="choice border-info-600 text-info-800" style="top: 0">
                                    <input type="radio" value="2" name="tipo_boleta" class="styled" disabled>
                                </div> Boleta Honorarios
                            </label>
                        </div>


                        <div class="col-sm-12">
                            <div id="mensaje_error_detalle" name="mensaje_error_detalle" class="has-error has-danger"></div>
                            <table class="table">
                                <thead>
                                <tr>
                                    <th width="110px;">Cantidad</th>
                                    <th>
                                        <div  id="descripcion_detalles">
                                            Descripción
                                        </div>
                                    </th>

                                    <th id="importe_primero">Importe Unitario s/IVA</th>
                                    <th id="tipo_impuesto" width="50px;">IVA</th>
                                    <th id="importe_segundo">Importe Unitario c/IVA</th>
                                </tr>
                                </thead>
                                <tbody id="body_product_oc">
                                <?php $count = 0; ?>
                                @foreach($details as $item)
                                    <?php $count++; ?>
                                    <tr id="product_item_{{$count}}" class="items">
                                        <td class="center-column"><input type="number" name="cant_{{$count}}" id="cant_{{$count}}" class="form-control cantidad paso-5" min="1" value="{{$item->quantity}}"  ></td>
                                        <td><input type="text" name="desc_{{$count}}" id="desc_{{$count}}" class="form-control paso-5" value="{{$item->description}}"  > </td>

                                        <td class="right-column"><input type="text" name="priceWithoutIva_{{$count}}" id="priceWithoutIva_{{$count}}" class="form-control money paso-5"  value="{{ $item->id_currency == 2 ? number_format($item->price,0,",","."): str_replace('.',',',$item->price) }}"></td>
                                        <td id="checkiva_row_{{$count}}">

                                            <?php $checked = $item->has_iva == 1 ?  "checked": false ?>
                                            {{ Form::checkbox('iva_'.$count, null,$item->has_iva == 1 ?  $item->has_iva: false, ['class' => 'styled checkiva paso-5 '.$checked,'id'=>"iva_".$count,'data-action'=>$count] ) }}
                                        </td>
                                        <td class="right-column">
                                            @if ($item->has_iva == 1)
                                                <input type="text" name="priceWithIva_{{$count}}" id="priceWithIva_{{$count}}" class="form-control money paso-5" value="{{ $item->id_currency == 2 ? number_format($item->price_iva,0,",","."): str_replace('.',',',$item->price_iva)}}">
                                            @else
                                                <input type="text" name="priceWithIva_{{$count}}" id="priceWithIva_{{$count}}" class="form-control money paso-5" disabled="disabled" value="0">
                                            @endif
                                        </td>
                                        <td><a class="delete_item paso-5" data-action="{{$count}}"><i class="fa fa-trash-o fa-lg " aria-hidden="true"></i></a></td>
                                    </tr>
                                    <script></script>
                                @endforeach
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
                        <button id="guardar" name="guardar" type="button" class="btn btn-success ">
                            <i class="fa fa-cogs"></i> Editar Orden de Compra
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

@endsection

@section('scripts')
    <script>let id_area = '<?php echo $order->id_area; ?>'</script>
    <script> let actual_count = '<?php echo $count; ?>'</script>
    <script> let total_price = '<?php echo $total_price; ?>'</script>
    <script> let old_folio_number = '<?php echo $order->old_folio_number ?>'</script>
    <script>console.log(actual_count);</script>
    {!!Html::script('js/views/purchaseOrderAccions.js')!!}
    {!!Html::script('js/views/editPurchaseOrder.js')!!}
@endsection