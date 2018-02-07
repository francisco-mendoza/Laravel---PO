@extends('layout.principal')

@section('content')



<h2 class="page-header">Contratos</h2>
<br />
<div class="panel panel-default">
    <div class="panel-heading">
        <span class="tituloPantalla">Detalle de Contrato  </span>  </div>

    <div class="panel-body">
                

        <form action="{{ url('/contracts') }}" method="POST" class="form-horizontal">


                
        <div class="form-group">
            <label for="id_contract" class="col-sm-3 control-label">Id de Contrato</label>
            <div class="col-sm-6">
                <input type="text" name="id_contract" id="id_contract" class="form-control" value="{{$model['id_contract'] or ''}}" readonly="readonly">
            </div>
        </div>
        
                
        <div class="form-group">
            <label for="id_provider" class="col-sm-3 control-label">Proveedor</label>
            <div class="col-sm-6">
                <input type="text" name="id_provider" id="id_provider" class="form-control" value="{{$provider['name_provider'] or ''}}" readonly="readonly">
            </div>
        </div>

        <div class="form-group">
            <label for="start_date" class="col-sm-3 control-label">Número de Contrato</label>
            <div class="col-sm-6">
                <input type="text" name="contract_number" id="contract_number" class="form-control" value="{{$model['contract_number'] or ''}}" required maxlength="128">

            </div>
        </div>

        <div class="form-group">
            <label for="start_date" class="col-sm-3 control-label">Descripción</label>
            <div class="col-sm-6">
                <input type="text" name="description" id="description" class="form-control" value="{{$model['description'] or ''}}" maxlength="128">

            </div>
        </div>


        <div class="form-group form-inline">
            <label class="col-sm-3 control-label">Contrato Activo? </label>
            <div class="col-sm-1 border-info-600 text-info-800" style="padding-top: 6px;">
                {{ Form::checkbox('is_active', null,  $model->is_active,['readonly'=>'readonly', 'disabled'=>'disabled', 'class' => 'switchery-info ']) }}</div>
            @if($model->is_active)
                <label class="col-sm-1 control-label">Desde: </label>
                <div class="col-sm-3"><input class="form-control" type="text" value="{{date('d-m-Y', strtotime($model->start_date))}}" readonly></div>
            @endif
        </div>



        <div class="form-group">
            <label for="end_date" class="col-sm-3 control-label">Fecha de Finalización</label>
            @if($model['end_date'] == "" || $model['end_date'] == null)
            <div class="col-sm-3">
                <input type="text" name="end_date" id="end_date" class="form-control" value="{{'No ha finalizado el contrato'}}" readonly="readonly">
            </div>
            @else
                <div class="col-sm-3">
                    <input type="text" name="end_date" id="end_date" class="form-control" value="{{date('d-m-Y', strtotime($model->end_date))}}" readonly="readonly">
                </div>
            @endif
        </div>

            <div class="form-group ">

                <div class="col-sm-2">

                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <span class="tituloPantalla">Cuentas</span>   </div>

                    <div class="panel-body">
                        <div class="form-group">
                            <div class="col-sm-10 col-sm-offset-1">
                                <div id="mensaje_error_detalle" name="mensaje_error_detalle" class="has-error has-danger">
                                </div>
                                <table id="cuentasContrato" class="table table-striped  table-condensed table-framed table-hover full-width " style="align-content: center">
                                    <thead>
                                    <tr>
                                        <th style="width: 20%">Area</th>
                                        <th style="width: 20%">Cuenta</th>
                                        <th style="width: 10%">Año</th>
                                        <th style="width: 10%"></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </div>


        <div class="form-group">
            <div class="col-sm-offset-3 col-sm-6">
                <a class="btn btn-default" href="{{ url('/contracts') }}"><i class="glyphicon glyphicon-chevron-left"></i> Atrás</a>
            </div>
        </div>


        </form>

    </div>
</div>

@endsection

@section('scripts')
    <script type="text/javascript" >
        var consultaCuentas = "{{ url('/contracts/getAccounts/'.( isset($model) ? $model->id_contract : "")) }}";
        var idContract = "{{isset($model) ? $model->id_contract : ""}}";
        var editar = "{{Entrust::can('editar_contratos')}}";
        var eliminar = "{{Entrust::can('eliminar_contratos')}}";
        var verPDF = "{{Entrust::can('ver_pdf_contratos')}}";
    </script>
    <script type="text/javascript" src="/js/views/contracts.js"></script>
@endsection