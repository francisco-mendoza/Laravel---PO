@extends('layout.principal')

@section('content')



<h2 class="page-header">Área</h2>
<br />
<div class="panel panel-default">
    <div class="panel-heading">
        <span class="tituloPantalla">Detalle de Área  </span>  </div>

    <div class="panel-body">
                

        <form action="{{ url('/areas') }}" method="POST" class="form-horizontal">


                
        <div class="form-group">
            <label for="id_area" class="col-sm-3 control-label">Id Area</label>
            <div class="col-sm-6">
                <input type="text" name="id_area" id="id_area" class="form-control" value="{{$model['id_area'] or ''}}" readonly="readonly">
            </div>
        </div>
        
                
        <div class="form-group">
            <label for="short_name" class="col-sm-3 control-label">Abreviatura</label>
            <div class="col-sm-6">
                <input type="text" name="short_name" id="short_name" class="form-control" value="{{$model['short_name'] or ''}}" readonly="readonly">
            </div>
        </div>
        
                
        <div class="form-group">
            <label for="long_name" class="col-sm-3 control-label">Nombre de Área</label>
            <div class="col-sm-6">
                <input type="text" name="long_name" id="long_name" class="form-control" value="{{$model['long_name'] or ''}}" readonly="readonly">
            </div>
        </div>
        
                
        <div class="form-group">
            <label for="manager_name" class="col-sm-3 control-label">Gerente</label>
            <div class="col-sm-6">
                <input type="text" name="manager_name" id="manager_name" class="form-control" value="{{$model['manager_name'] or ''}}" readonly="readonly">
            </div>
        </div>
        
                
        <div class="form-group">
            <label for="manager_position" class="col-sm-3 control-label">Cargo Gerente</label>
            <div class="col-sm-6">
                <input type="text" name="manager_position" id="manager_position" class="form-control" value="{{$model['manager_position'] or ''}}" readonly="readonly">
            </div>
        </div>

        <div class="form-group " >

                <div class="panel panel-default">
                    <div class="panel-heading">
                        <span class="tituloPantalla">Presupuestos Anuales </span>   </div>

                    <div class="panel-body ">
                        <div class="col-sm-8 col-sm-offset-2">
                            <table id="montosAreas" class="table table-striped  table-condensed table-framed table-hover  " style="align-content: center">
                            <thead>
                            <tr>
                                <th style="width: 20%">Año</th>
                                <th class="center-column" style="width: 40%">Presupuesto Anual</th>
                                <th class="center-column" style="width: 40%">Monto Disponible</th>
                            </tr>
                            </thead>
                            <tbody>
                                @if(count($budgets) > 0)
                                @foreach($budgets as $budget)
                                    <td>
                                        {{$budget->budget_year}}
                                    </td>
                                    <td class="right-column">
                                        {{number_format($budget->total_budget_initial,2,',','.')}}
                                    </td>
                                    <td class="right-column">
                                        {{number_format($budget->total_budget_available,2,',','.')}}
                                    </td>
                                @endforeach
                                @else
                                <td colspan="3" class="center-column"> No hay registros disponibles</td>
                                @endif
                            </tbody>
                        </table>
                        </div>

                        @if($model['budget_closed'] == 1)
                            <div class=" form-group col-sm-12">
                                </br>
                                <label class="col-sm-offset-8 control-label text-bold " style="color:#D84315">Presupuesto Cerrado</label>
                            </div>
                        @endif


                        @if(count($accounts) > 0 && $budget->total_budget_available > 0)
                            <div class="col-sm-8 col-sm-offset-2">
                                </br>
                                <table id="montosCuentas" class="table table-striped  table-condensed table-framed table-hover  " style="align-content: center">
                                    <thead>
                                    <tr>
                                        <th style="width: 20%">Cuenta</th>
                                        <th class="center-column" style="width: 40%">Presupuesto Anual</th>
                                        <th class="center-column" style="width: 40%">Monto Disponible</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if(count($accounts) > 0)
                                        @foreach($accounts as $account)
                                            @if($account->total_budget_available > 0)
                                                <tr>
                                                    <td>
                                                        {{$account->account_code}}
                                                    </td>
                                                    <td class="right-column">
                                                        {{number_format($account->total_budget_initial,2,',','.')}}
                                                    </td>
                                                    <td class="right-column">
                                                        {{number_format($account->total_budget_available,2,',','.')}}
                                                    </td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    @endif
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
        </div>
        
        
        <div class="form-group">
            <div class="col-sm-offset-3 col-sm-6">
                <a class="btn btn-default" href="{{ url('/areas') }}"><i class="glyphicon glyphicon-chevron-left"></i> Atrás</a>
            </div>
        </div>


        </form>

    </div>
</div>







@endsection