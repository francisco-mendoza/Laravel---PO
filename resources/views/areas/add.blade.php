@extends('layout.principal')

@section('content')




<h2 class="page-header">Área</h2>
<br />
<div class="panel panel-default">
    <div class="panel-heading">
        <span class="tituloPantalla">Agregar/Editar Área </span>   </div>

    <div class="panel-body">
                
        <form id="f_areas" action="{{ url('/areas'.( isset($model) ? "/" . $model->id_area : "")) }}" method="POST" class="form-horizontal">
            {{ csrf_field() }}

            @if (isset($model))
                <input type="hidden" name="_method" value="PATCH">
                <input type="hidden" name="id_area" id="id_area" value="{{$model->id_area}}">
            @endif
           <input type="hidden" name="id_user" id="id_user" value="">
           <div class="form-group">
                <label for="short_name" class="col-sm-3 control-label">Abreviatura</label>
                <div class="col-sm-6">
                    <input type="text" name="short_name" id="short_name" class="form-control" value="{{$model['short_name'] or ''}}" required maxlength="16">
                    <div class="help-block with-errors"></div>
                    @if($errors->has('short_name'))
                        <span class="text-danger">{{ $errors->first('short_name') }}</span>
                    @endif
                </div>
            </div>
            <div class="form-group">
                <label for="long_name" class="col-sm-3 control-label">Nombre de Área</label>
                <div class="col-sm-6">
                    <input type="text" name="long_name" id="long_name" class="form-control" value="{{$model['long_name'] or ''}}" required maxlength="128">
                    <div class="help-block with-errors"></div>
                    @if($errors->has('long_name'))
                        <span class="text-danger">{{ $errors->first('long_name') }}</span>
                    @endif
                </div>
            </div>
            <div class="form-group">
                <label for="manager_name" class="col-sm-3 control-label">Gerente</label>
                <div class="col-sm-6">
                    <input type="text" name="manager_name" id="manager_name" placeholder="Ingrese Nombre de Usuario" class="form-control typeahead" value="{{$model['manager_name'] or ''}}" required maxlength="128" data-contains >
                    <div class="help-block with-errors"></div>
                    @if($errors->has('manager_name'))
                        <span class="text-danger">{{ $errors->first('manager_name') }}</span>
                    @endif
                </div>
            </div>
            <div class="form-group">
                <label for="manager_position" class="col-sm-3 control-label">Cargo Gerente</label>
                <div class="col-sm-6">
                    <input type="text" name="manager_position" id="manager_position" class="form-control" value="{{$model['manager_position'] or ''}}" required maxlength="128">
                    <div class="help-block with-errors"></div>
                    @if($errors->has('manager_position'))
                        <span class="text-danger">{{ $errors->first('manager_position') }}</span>
                    @endif
                </div>
            </div>
            <input type="hidden" name="is_closed" id="is_closed" value="{{$model['budget_closed'] or '0'}}">
            <input type="hidden" name="total_budget_html" id="total_budget_html" value="">




            <div class="form-group " >

                <div class="col-sm-2">

                </div>


                <div class="panel panel-default">
                    <div class="panel-heading">
                        <span class="tituloPantalla">Presupuestos Anuales por Cuenta</span>   </div>

                    <div class="panel-body">

                        <div class="form-group">
                            <div class="col-sm-2">
                                <button type="button" id="add_budget" class="btn btn-primary " ><i class="fa fa-plus-circle fa-lg" aria-hidden="true"></i> Agregar Cuenta </button>
                            </div>
                            @if(isset($model['budget_closed']) && $model['budget_closed'] == 1)
                                <div class="col-sm-8 row">
                                    <label class="control-label col-sm-4 text-bold  " style="text-align: right !important" for="total_free" >Presupuesto No Asignado:</label>
                                    <div class="col-sm-4" id="total_free_div"><input class="form-control " type="text" name="total_free" id="total_free" value="0" readonly >

                                    </div>
                                    <i id="icon-alert" class=" fa fa-exclamation-circle fa-2x text-success " aria-hidden="true" style="padding-top: 5px; display:none;" ></i>
                                </div>
                            @endif



                        </div>

                        <div  class="form-group">

                            <div class="col-sm-12"  id="budget_detail">
                                <div>
                                    <div class="col-sm-2">
                                        <label for="year_budget" class="col-sm-1 paso-2 control-label">Año:</label>
                                        <input type="number" name="year_budget" id="year_budget"  class="center-column form-control paso-2" value="{{getdate()['year']}}"  maxlength="4" readonly>
                                    </div>
                                    <div class="col-sm-2">
                                        <label for="account_code" class="col-sm-3 paso-2 control-label">Código:</label>
                                        <input type="text" name="account_code" id="account_code"  class="form-control  paso-2" maxlength="10" disabled>
                                    </div>
                                    <div class="col-sm-2">
                                        <label for="account_name" class="col-sm-2 paso-2 control-label">Nombre:</label>
                                        <input type="text" name="account_name" id="account_name"  class="form-control  paso-2" maxlength="100" disabled>
                                    </div>
                                    <div class="col-sm-3">
                                        <label for="description" class="col-sm-3 paso-2 control-label">Descripción:</label>
                                        <input type="text" name="description" id="description"  class="form-control  paso-2" maxlength="140" disabled>
                                    </div>
                                    <div class="col-sm-2">
                                        <label for="initial_budget" class="col-sm-2 paso-2 control-label">Monto:</label>
                                        <input type="text" name="initial_budget" id="initial_budget" placeholder="Ej: $ 1.000.000,00" class="form-control money paso-2" maxlength="128" disabled>
                                    </div>
                                    <div class="col-sm-1">
                                        <a class="paso-2" id="add_budget_detail" disabled="true"><i class="fa fa-plus-circle fa-2x paso-2" aria-hidden="true" style="padding: 5px;padding-top: 37px; "></i></a>
                                    </div>


                                </div>



                            </div>

                        </div>

                        <hr>

                        <div class="form-group">

                            <div class="col-sm-12 ">

                                <div id="mensaje_error_detalle" name="mensaje_error_detalle" class="has-error has-danger">

                                </div>

                                <table id="montosAreas" class="table table-striped  table-condensed table-framed table-hover hover full-width table-accounts " style="align-content: center">
                                    <thead>
                                    <tr>
                                        <th style="width:5%">Año</th>
                                        <th style="width:5%">Código</th>
                                        <th style="width: 30% ; max-width: 220px;">Nombre</th>
                                        <th style="width: 32% ; max-width: 240px;">Descripción</th>
                                        <th class="center-column" style="width: 15%; max-width: 112px;">Monto</th>
                                        <th class="center-column" style="width: 15%; max-width: 112px; padding:2px">No Asignado OC</th>
                                        <th style="width:5%">Editar</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>

                        </div>

                        <div class="form-group">
                            <div class="form-group col-sm-12">
                                <label class="col-sm-1 col-sm-offset-9 control-label" style="padding-left:0px;padding-right: 0px;">Fijar Budget</label>
                                <div data-toggle="tooltip" title="Una vez fijado el budget no se podrá modificar." class="col-sm-2 border-info-600 text-info-800" style="padding-top:5px; ">
                                    @if(isset($model) && $model->budget_closed == 1)
                                        {{ Form::checkbox('budget_closed', null,  true,['class' => 'switchery-info  ', 'readonly' => 'readonly', 'id' =>'budget_closed']) }}
                                    @else
                                        {{ Form::checkbox('budget_closed', null,  false,['class' => 'switchery-info ', 'id' =>'budget_closed']) }}
                                    @endif
                                </div>
                            </div>
                        </div>


                    </div>
                </div>

            </div>

            <br/>
            <br/>

            <input type="hidden" id="budgets" name="budgets" value="">

            <div class="form-group">
                <div class="col-sm-offset-9 col-sm-3">
                    <a class="btn btn-default" href="{{ url('/areas') }}"><i class="glyphicon glyphicon-chevron-left"></i> Atrás</a>
                    <button id="guardar" name="guardar" type="submit" class="btn btn-success">
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
        var consulta ="{{url('areas/grid')}}";
        var areas = "{{url('/areas')}}";

        var consultaPresupuesto = "{{ url('/areas/getBudgets/'.( isset($model) ? $model->id_area : "")) }}";
        var idArea = "{{isset($model) ? $model->id_area : ""}}";
    </script>

    <script type="text/javascript" src="/js/views/add_areas.js"></script>


@endsection