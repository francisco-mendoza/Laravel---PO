@extends('layout.principal')

@section('content')



<h2 class="page-header">Proveedor</h2>
<br />
<div class="panel panel-default">
    <div class="panel-heading">
        <span class="tituloPantalla">Detalle de Proveedor </span>   </div>

    <div class="panel-body">
                

        <form action="{{ url('/providers') }}" method="POST" class="form-horizontal">


        <div>
            <h4>Datos de Proveedor</h4>
        </div>
        <div class="form-group">
            <label for="id_provider" class="col-sm-3 control-label">Id de Proveedor</label>
            <div class="col-sm-6">
                <input type="text" name="id_provider" id="id_provider" class="form-control" value="{{$model->id_provider or ''}}" readonly="readonly">
            </div>
        </div>
        
                
        <div class="form-group">
            <label for="name_provider" class="col-sm-3 control-label">Razón Social</label>
            <div class="col-sm-6">
                <input type="text" name="name_provider" id="name_provider" class="form-control" value="{{$model->name_provider or ''}}" readonly="readonly">
            </div>
        </div>
        
                
        <div class="form-group">
            <label for="business" class="col-sm-3 control-label">Negocio</label>
            <div class="col-sm-6">
                <input type="text" name="business" id="business" class="form-control" value="{{$model->business or ''}}" readonly="readonly">
            </div>
        </div>
        
                
        <div class="form-group">
            <label for="rut" class="col-sm-3 control-label">RUT</label>
            <div class="col-sm-6">
                <input type="text" name="rut" id="rut" class="form-control" value="{{$model->rut or ''}}" readonly="readonly">
            </div>
        </div>
        
                
        <div class="form-group">
            <label for="address" class="col-sm-3 control-label">Dirección</label>
            <div class="col-sm-6">
                <input type="text" name="address" id="address" class="form-control" value="{{$model->address or ''}}" readonly="readonly">
            </div>
        </div>
        
                
        <div class="form-group">
            <label for="phone" class="col-sm-3 control-label">Teléfono</label>
            <div class="col-sm-6">
                <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-phone fa-lg" ></i></span>
                    <input type="text" name="phone" id="phone" class="form-control" value="{{$model->phone or ''}}" readonly="readonly">
                    </div>
            </div>
        </div>


            <div class="form-group">
                <label for="payment_conditions" class="col-sm-3 control-label">Condición de Pago</label>
                <div class="col-sm-6">
                    <input type="text" name="payment_conditions" id="payment_conditions" class="form-control" value="{{$model->name_condition or 'Condición de pago no asignada'}}" readonly="readonly">
                </div>
            </div>


            <div class="form-group">
                <label for="payment_method" class="col-sm-3 control-label">Método de Pago</label>
                <div class="col-sm-6">
                    <input type="text" name="payment_method" id="payment_method" class="form-control" value="{{$model->name_method or 'Método de pago no asignado'}}" readonly="readonly">
                </div>
            </div>


            <div class="form-group">
                <label for="bank" class="col-sm-3 control-label">Banco Asociado</label>
                <div class="col-sm-6">
                    <input type="text" name="bank" id="bank" class="form-control" value="{{$model->bank or ''}}" readonly="readonly">
                </div>
            </div>


            <div class="form-group">
                <label for="type_account" class="col-sm-3 control-label">Tipo de Cuenta</label>
                <div class="col-sm-6">
                    <input type="text" name="type_account" id="type_account" class="form-control" value="{{$model->type_account or ''}}" readonly="readonly">
                </div>
            </div>


            <div class="form-group">
                <label for="number_account" class="col-sm-3 control-label">Número de Cuenta</label>
                <div class="col-sm-6">
                    <input type="text" name="number_account" id="number_account" class="form-control" value="{{$model->number_account or ''}}" readonly="readonly">
                </div>
            </div>

            <div class="form-group">
                <label for="is_visible" class="col-sm-3 control-label">Proveedor Activo?</label>

                <div class="col-sm-6 checker border-info-600 text-info-800">{{ Form::checkbox('is_visible', null,  $model->is_visible,['readonly'=>'readonly', 'disabled'=>'disabled', 'class' => 'styled ']) }}</div>
            </div>

        <br />
        <div>
            <h4>Datos de Persona de Contacto</h4>
        </div>
        <div class="form-group">
            <label for="contact_name" class="col-sm-3 control-label">Nombre y Apellido</label>
            <div class="col-sm-6">
                <input type="text" name="contact_name" id="contact_name" class="form-control" value="{{$model->contact_name or ''}}" readonly="readonly">
            </div>
        </div>
        
                
        <div class="form-group">
            <label for="contact_area" class="col-sm-3 control-label">Cargo </label>
            <div class="col-sm-6">
                <input type="text" name="contact_area" id="contact_area" class="form-control" value="{{$model->contact_area or ''}}" readonly="readonly">
            </div>
        </div>
        
                
        <div class="form-group">
            <label for="contact_email" class="col-sm-3 control-label">Correo </label>
            <div class="col-sm-6">
                <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-envelope-o" ></i></span>
                    <input type="text" name="contact_email" id="contact_email" class="form-control" value="{{$model->contact_email or ''}}" readonly="readonly">
                    </div>
            </div>
        </div>
        
                
        <div class="form-group">
            <label for="contact_phone" class="col-sm-3 control-label">Teléfono</label>
            <div class="col-sm-6">
                <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-phone fa-lg" ></i></span>
                    <input type="text" name="contact_phone" id="contact_phone" class="form-control" value="{{$model->contact_phone or ''}}" readonly="readonly">
                    </div>

            </div>
        </div>
        
                

        
                

        
        
        <div class="form-group">
            <div class="col-sm-offset-3 col-sm-6">
                <a class="btn btn-default" href="{{ url('/providers') }}"><i class="glyphicon glyphicon-chevron-left"></i> Atrás</a>
            </div>
        </div>


        </form>

    </div>
</div>







@endsection