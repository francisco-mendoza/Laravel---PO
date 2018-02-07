@extends('layout.principal')

@section('content')

    <h2 class="page-header">Buscar Órdenes de Compra</h2>

    </br>
    <div class="panel panel-default">
        <div class="panel-heading">
            <span class="tituloPantalla">Listado de Órdenes Filtradas</span>
        </div>

        <div class="panel-body">
            </br>
            <div class="">
                <table class="table table-striped table-framed table-hover full-width" id="filteredOrdersGrid" >
                    <thead>
                    <tr>
                        <th  ></th>
                        <th><i class="fa fa-sitemap" ></i> Area</th>
                        <th width="100px"><i class="fa fa-user" ></i> Usuario</th>
                        <th>Nro. Orden</th>
                        <th>Proveedor</th>
                        <th style="width:90px" class="center-column"> Importe</th>
                        <th style=" padding-right: 24px !important;" class="center-column">Fecha</th>
                        <th>Estado</th>
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
        var consulta ="{{url('/purchaseOrder/filter')}}";
        var detail ="{{url('/detailPurchaseOrder') }}";
        var patron = "{{Session::get('patronBusquedaAvanzada')}}";

    </script>

    <script type="text/javascript" src="/js/views/filterPurchaseOrders.js"></script>

@endsection