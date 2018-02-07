@extends('layout.principal')

@section('content')


<h2 class="page-header">Facturas</h2>
<br>
<div class="panel panel-default">
    <div class="panel-heading">
        Agregar/Editar Factura
    </div>
    <div class="panel-body">

        <form class="steps-basic " action="{{ url('/invoices'.( isset($model) ? "/" . $model->id_invoice : "")) }}" method="POST" id="f_invoice" name="f_invoice" >
            {{ csrf_field() }}

            @if (isset($model))
            <input type="hidden" name="_method" value="PATCH">
            <input type="hidden" name="id_invoice" id="id_invoice" value="{{$model->id_invoice}}">
            @endif

            <h6>Datos de la Factura</h6>
            <fieldset>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Proveedor</label>
                            <input type="text" name="id_provider" id="id_provider" placeholder="Seleccione un proveedor" class="form-control typeahead" value="{{$provider['name_provider'] or ''}}" data-containsProvider>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="id_provider">Area</label>
                            {{ Form::select('id_area', $areas,  isset($select_area->id_area) ? $select_area->id_area : null , ['class' => 'form-control paso-2','placeholder' => 'Seleccione un área','name'=>'id_area', 'id'=>'id_area'])  }}
                        </div>
                    </div>


                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="id_provider">N° Factura</label>
                            <input type="text" id="id_document" name="id_document" class="form-control" value="{{$model->id_document or ''}}" {{isset($model) ? "readonly" : ""}}>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">

                            <div class="form-group">
                                <label>Periodo de Facturación</label>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            {{Form::selectRange('number',1,31, isset($model->billing_day) ? $model->billing_day : null, ['class' => 'form-control','placeholder' => 'Día','name'=>'billing_day', 'id'=>'billing_day'])}}
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            {{ Form::select('billing_month', $months,  isset($model->billing_month) ? $model->billing_month : null , ['class' => 'form-control','placeholder' => 'Mes','name'=>'billing_month', 'id'=>'billing_month'])  }}
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            {{ Form::select('billing_year', $years,  isset($model->billing_year) ? $model->billing_year : null , ['class' => 'form-control','placeholder' => 'Año','name'=>'billing_year', 'id'=>'billing_year'])  }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="id_provider">Tipo Moneda</label>
                            {{ Form::select('currency',$currencies, isset($model->id_currency) ? $model->id_currency : 2 , ['class' => 'form-control','name'=>'currency', 'id'=>'currency', 'required' => 'true','data-selectCurrency' => 'true'])  }}
                        </div>
                    </div>

                    <div class="col-md-6" >
                        <div class="form-group">

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Total</label>
                                        <input type="text" name="total" id="total" class="form-control money text-right" placeholder="" data-mask="" value="{{$model->total or ''}}" >
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Total c/Impuesto</label>
                                        <input type="text" name="total_impuesto" id="total_impuesto" class="form-control money text-right" placeholder="" data-mask="" value="{{$model['total_iva'] or ''}}">
                                    </div>
                                </div>

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
        var monto_minimo = "{{ isset($min_total)? $min_total->total : 0  }}";
    </script>

    <script type="text/javascript" src="/js/views/add_invoices.js"></script>
    <script type="text/javascript" src="/assets/template/js/plugins/forms/validation/validate.min.js"></script>

@endsection