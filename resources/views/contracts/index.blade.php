@extends('layout.principal')

@section('content')


<h2 class="page-header">Contratos</h2>

<br />
<div class="panel panel-default">
    <div class="panel-heading">
        <span class="tituloPantalla">Listado de Contratos </span>
    </div>
    {{ csrf_field() }}
    <div class="panel-body">
        @permission('crear_contratos')
            <div class="row">
                <a href="{{url('contracts/create')}}" class="btn btn-primary" role="button"><i class="fa fa-plus-square-o fa-lg" ></i> Agregar Contrato</a>
            </div>
        @endpermission

        <br />
        <div class="">
            <table class="table table-striped table-framed table-hover full-width" id="thegrid">
              <thead>
                <tr>
                    <th>Id Contrato</th>
                    <th>Nro. de Contrato</th>
                    <th>Proveedor</th>
                    <th>Activo?</th>
                    <th>Finalización</th>
                    <th>Path PDF</th>
                    <th style="width:50px">Ver PDF</th>
                    <th style="width:50px">Editar</th>
                    <th style="width:50px">Eliminar</th>
                </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
        </div>

    </div>
</div>




@endsection



@section('scripts')
    <script type="text/javascript" >
        var consulta ="{{url('contracts/grid')}}";
        var contratos = "{{url('/contracts')}}";
        var editar = "{{Entrust::can('editar_contratos')}}";
        var eliminar = "{{Entrust::can('eliminar_contratos')}}";
        var verPDF = "{{Entrust::can('ver_pdf_contratos')}}";

    </script>
    <script type="text/javascript" src="js/views/contracts.js"></script>
@endsection