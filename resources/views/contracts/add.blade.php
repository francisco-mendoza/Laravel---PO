@extends('layout.principal')

@section('content')


<h2 class="page-header">Contrato</h2>
<br />
<div class="panel panel-default">
    <div class="panel-heading">
        <span class="tituloPantalla">Agregar/Editar Contrato  </span> </div>

    <div class="panel-body">
                
        <form id="f_contrato" name="f_contrato" action="{{ url('/contracts'.( isset($model) ? "/" . $model->id_contract : "")) }}" method="POST" class="form-horizontal" enctype="multipart/form-data">
            {{ csrf_field() }}

            @if (isset($model))
                <input type="hidden" name="_method" value="PATCH">
                <input type="hidden" name="id_contract" id="id_contract" value="{{$model->id_contract}}">
            @endif


            <div class="form-group">
                <label for="id_provider" class="col-sm-3 control-label">Proveedor</label>
                <div class="col-sm-6">
                    <input type="text" name="id_provider" id="id_provider" placeholder="Seleccione un proveedor" class="form-control typeahead" value="{{$provider['name_provider'] or ''}}" required maxlength="128" data-containsProvider>
                    <div class="help-block with-errors"></div>
                    @if ($errors->has('id_provider'))
                        <div class="alert alert-danger">
                            {{$errors->first('id_provider')}}
                        </div>
                    @endif
                </div>
            </div>
            <div class="form-group">
                <label for="start_date" class="col-sm-3 control-label">Número de Contrato</label>
                <div class="col-sm-6">
                    <input type="text" name="contract_number" id="contract_number" class="form-control" value="{{$model['contract_number'] or ''}}" required maxlength="128">
                    <div class="help-block with-errors" id="contract_number_errors"></div>
                    @if ($errors->has('contract_number'))
                        <div class="alert alert-danger">
                            {{$errors->first('contract_number')}}
                        </div>
                    @endif
                </div>
            </div>
            <div class="form-group">
                <label for="start_date" class="col-sm-3 control-label">Descripción</label>
                <div class="col-sm-6">
                    <input type="text" name="description" id="description" class="form-control" value="{{$model['description'] or ''}}" maxlength="128">
                    <div class="help-block with-errors"></div>
                    @if ($errors->has('description'))
                        <div class="alert alert-danger">
                            {{$errors->first('description')}}
                        </div>
                    @endif
                </div>
            </div>
            {{--<div class="form-group">--}}
                {{--<label for="start_date" class="col-sm-3 control-label">Área</label>--}}
                {{--<div class="col-sm-6">--}}
                        {{--{{ Form::select('contract_area', $areas,  isset($model->contract_area) ? $model->contract_area : null , ['class' => 'form-control ','placeholder' => 'Seleccione un área','name'=>'contract_area', 'id'=>'contract_area'])  }}--}}

                        {{--<div class="help-block with-errors"></div>--}}
                        {{--@if ($errors->has('contract_area'))--}}
                            {{--<div class="alert alert-danger">--}}
                                {{--{{$errors->first('contract_area')}}--}}
                            {{--</div>--}}
                        {{--@endif--}}


                {{--</div>--}}
            {{--</div>--}}
            @if(isset($model->end_date) && $model->end_date != null)
            <div class="form-group">
                <label for="end_date" class="col-sm-3 control-label">Fecha de Finalización</label>
                <div class="col-sm-3">
                    <div class="input-group">
                        <input type="text" name="end_date" id="end_date" class="form-control daterange-single" value="{{( date('d/m/Y', strtotime($model['end_date'])) )}}">
                        <span class="input-group-addon"><i class="icon-calendar22" ></i></span>
                    </div>

                </div>
            </div>
            @else
                <div class="form-group">
                    <label for="end_date" class="col-sm-3 control-label">Fecha de Finalización</label>
                    <div class="col-sm-3">
                        <div class="input-group">
                            <input type="text" name="end_date" id="end_date" class="form-control daterange-single" value="">
                            <span class="input-group-addon"><i class="icon-calendar22" ></i></span>
                        </div>
                    </div>
                </div>
            @endif

            <div class="form-group" id="errorActivacion" hidden>
                <label class="alert-danger col-sm-6 col-lg-offset-3"  >La fecha de finalización del contrato es menor que la fecha de activación.</label>
            </div>

            @if(isset($model->contract_pdf) && $model->contract_pdf != null)
                <div class="form-group">
                    <label for="start_date" class="col-sm-3 control-label">PDF Contrato</label>
                    <div class="col-sm-6">
                        <input type="text" name="contract" id="contract" class=" form-control" value="{{$model['contract_pdf'] or ''}}" readonly >
                    </div>
                </div>
            @else
                <div class="form-group">
                    <label for="start_date" class="col-sm-3 control-label">PDF Contrato</label>
                    <div class="col-sm-6">
                        <input type="file" name="contract_pdf" id="contract_pdf" class="file-loading form-control" value="{{$model['contract_pdf'] or ''}}" data-preview-file-type="text">
                    </div>
                </div>
            @endif
            <div class="form-group ">
                <label for="start_date" class="col-sm-3 control-label">Activar Contrato</label>
                <div class=" col-sm-9  border-info-600 text-info-800">
                    {{ Form::checkbox('is_active', null,isset($model) ?  $model->is_active : false, ['class' => 'switchery-info ', 'id' => 'is_active'] ) }}
                </div>

            </div>

            {{--<div class="row" >--}}

            {{--</div>--}}


            <div class="form-group ">

                <div class="col-sm-2">

                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <span class="tituloPantalla">Cuentas</span>   </div>

                    <div class="panel-body">

                        <div class="form-group">
                            <div class="col-sm-2">
                                <button type="button" id="add_account" class="btn btn-primary "><i class="fa fa-plus-circle fa-lg" aria-hidden="true"></i> Agregar Cuenta </button>
                            </div>
                        </div>

                        <div  class="form-group">
                            <div class="col-sm-12"  id="account_detail">
                                <div>
                                    <div class="col-sm-3">
                                        <label for="manager_position" class="col-sm-3 paso-2 control-label">Área</label>
                                        {{--<input type="text" name="id_area" id="id_area" placeholder="Click para ver áreas" class="form-control paso-2"  disabled>--}}
                                        {{ Form::select('contract_area', $areas,  isset($model->contract_area) ? $model->contract_area : null , ['class' => 'form-control paso-2','placeholder' => 'Seleccione un área','name'=>'account_area', 'id'=>'account_area','disabled'=>'disabled'])  }}

                                    </div>

                                    <div class="col-sm-2">
                                        <label for="" class="col-sm-3 paso-2 control-label">Cuenta:</label>
                                        <input type="text" name="account_code" id="account_code"  class="form-control money paso-2" maxlength="10" disabled>
                                    </div>

                                    <div class="col-sm-2">
                                        <label for="manager_position" class="col-sm-2 paso-2 control-label">Año:</label>
                                        <input type="number" name="account_year" id="account_year"  class="form-control" maxlength="4" readonly>
                                    </div>

                                    <div class="col-sm-1">
                                        <a class="paso-2" id="add_account_detail" disabled="true" data-action="false"><i class="fa fa-plus-circle fa-2x paso-2" aria-hidden="true" style="padding: 5px;padding-top: 37px "></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
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
                                        <th style="width: 10%">Editar</th>
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

            <br/>
            <br/>

            <input type="hidden" id="accounts" name="accounts" value="">
            <input type="hidden" id="edit_accounts" name="edit_accounts" value="false">

                                    
            <div class="form-group">
                <div class="col-sm-offset-3 col-sm-3">
                    <a class="btn btn-default" href="{{ url('/contracts') }}"><i class="glyphicon glyphicon-chevron-left"></i> Atrás</a>
                    <button type="button" name="guardar" id="guardar" class="btn btn-success">
                        <i class="fa fa-save"></i> Guardar
                    </button> 

                </div>
            </div>
        </form>

    </div>
</div>






@endsection

@section('scripts')
    <script type="text/javascript" >
        var consulta ="{{url('contracts/grid')}}";
        var contratos = "{{url('/contracts')}}";
        var consultaCuentas = "{{ url('/contracts/getAccounts/'.( isset($model) ? $model->id_contract : "")) }}";
        var idContract = "{{isset($model) ? $model->id_contract : ""}}";
        var edit_contract = true;
        var editar = "{{Entrust::can('editar_contratos')}}";
        var eliminar = "{{Entrust::can('eliminar_contratos')}}";
        var verPDF = "{{Entrust::can('ver_pdf_contratos')}}";
    </script>
    <script type="text/javascript" src="/js/views/contracts.js"></script>
@endsection