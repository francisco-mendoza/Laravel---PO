@extends('layout.principal')

@section('content')



    <h2 class="page-header">Facturas Pendientes</h2>
    <br />
    <div class="panel panel-default">
        <div class="panel-heading">
            Listado de Facturas Pendientes
        </div>
        {{ csrf_field() }}
        <div class="panel-body">

            <br />
            <div class="">
                <table class="table table-striped table-framed table-hover full-width" id="thePendingInvoices">
                    <thead>
                    <tr>
                        <th >N° Factura</th>
                        <th >Proveedor</th>
                        <th style="width:90px" class="center-column">Total</th>
                        <th style="width:90px" class="center-column">Facturado</th>
                        <th style="width:70px; padding-right: 24px !important;" class="center-column">Fecha</th>
                        <th style="width:60px">Asignar Ordenes</th>
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
    <script type="text/javascript">
        var consulta ="{{url('invoices/pendingGrid')}}";
        var facturas = "{{url('/invoices/addOC')}}";
        var detalle = "{{url('/invoices/')}}";
        var asignar = "{{Entrust::can('asignar_ordenes_a_factura')}}";
    </script>
    <script type="text/javascript" src="/js/views/pendingInvoices.js"></script>
@endsection