@extends('layout.principal')

@section('content')

    <h2 class="page-header">Aprobar Órdenes de Compra</h2>

    </br>
    <div class="panel panel-default">
        <div class="panel-heading">
            <span class="tituloPantalla">Listado de Órdenes</span>
        </div>
        <div class="panel-body">
            <div class="">
                <div id="titulo" hidden><h3>Órdenes Emitidas</h3></div>
                <table class="table table-striped table-framed table-hover full-width" id="approveOrders">
                    <thead>
                    <tr>
                        <th></th>
                        <th><i class="fa fa-sitemap" ></i> Área</th>
                        <th><i class="fa fa-user" ></i> Usuario</th>
                        <th>Nro. Orden</th>
                        <th width="80px" class="center-column"> Importe</th>
                        <th><i class="fa fa-calendar" ></i> Fecha</th>
                        <th>Estado</th>
                        <th width="20px">Validar</th>
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
        var consulta ="{{url('/purchaseOrder/grid')}}";
        var detail ="{{url('/detailPurchaseOrder') }}";
        var esUsuarioOwner = "{{UserAlias::needFilteringByArea()}}";
        var permisoAprobar = "{{Entrust::can('ver_aprobarOrdenes')}}";



    </script>

    <script type="text/javascript" src="/js/views/approvePurchaseOrders.js"></script>

@endsection