@extends('layout.principal')
@section('content')

    <h2 class="page-header">Consultar Órdenes de Compra</h2>

    </br>
    <div class="panel panel-default">
        <div class="panel-heading">
            <span class="tituloPantalla">Listado de Órdenes</span>
        </div>

        <div class="panel-body">
            @permission('ver_crearOrden')
            <div class="row">
                <a href="{{url('/crearOrdenes')}}" class="btn btn-primary " role="button">
                    <i class="fa fa-plus-square-o fa-lg" ></i> Agregar Orden de Compra</a>
            </div>
            @endpermission
            </br>
            <div class="">
                <div id="titulo" hidden><h3>Órdenes Emitidas</h3></div>

                <div id="botonesEditarEmitidas" style="padding-bottom: 10px;" >
                    @permission('editar_oc')
                        <a class=" btn bg-primary-300" id="editarEmitidas"><i class="icon-pencil" ></i> Editar</a>
                    @endpermission
                    @permission('eliminar_oc')
                        <a class=" btn bg-danger-300" id="eliminarEmitidas"><i class="icon-trash"></i> Eliminar</a>
                    @endpermission
                </div>

                <table class="table table-striped table-framed table-hover full-width" id="ordersGrid" >
                    <thead>
                    <tr>
                        <th style="padding:0px; width:20px !important" ></th>
                        <th><i class="fa fa-sitemap" ></i> Area</th>
                        <th><i class="fa fa-user" ></i> Usuario</th>
                        <th>Nro. Orden</th>
                        <th style="width:90px" class="center-column"> Importe</th>
                        <th style=" padding-right: 24px !important;" class="center-column">Fecha</th>
                        <th>Estado</th>
                        <th style="width:20px">Ver</th>
                        <th style="width:30px">Imprimir</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>

            <div id="tablaOrdenesAprobadas" hidden>
                <div><h3>Órdenes Aprobadas / Rechazadas</h3></div>
                <div id="botonesEditarAprobadas" style="padding-bottom: 10px;" >
                    @permission('editar_oc')
                        <a class=" btn bg-primary-300" id="editarAprobadas"><i class="icon-pencil" ></i> Editar</a>
                    @endpermission
                    @permission('eliminar_oc')
                        <a class=" btn bg-danger-300" id="eliminarAprobadas"><i class="icon-trash"></i> Eliminar</a>
                    @endpermission
                </div>
                <table class="table table-striped table-framed table-hover full-width" id="approvedOrdersGrid">
                    <thead>
                    <tr>
                        <th style="padding:0px; width:20px !important"></th>
                        <th><i class="fa fa-sitemap" ></i> Area</th>
                        <th><i class="fa fa-user" ></i> Usuario</th>
                        <th >Nro. Orden</th>
                        <th style="width:90px" class="center-column"> Importe</th>
                        <th style=" padding-right: 24px !important;" class="center-column">Fecha</th>
                        <th style="padding:0px">Estado</th>
                        <th style="width:20px">Ver</th>
                        <th style="width:30px">Imprimir</th>
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
        var consultaAprobadas ="{{url('/purchaseOrder/approvedGrid')}}";
        var areas ="{{url('/purchaseOrder/grid')}}"; //Asignar valor para los métodos de ver, imprimir y detalle

        var detail ="{{url('/detailPurchaseOrder') }}";

        var eliminar = "{{url('/purchaseOrder/deletePurchaseOrder')}}";

        var esUsuarioRegular = "{{UserAlias::needFilteringByUser()}}";
        var permisoEditar = "{{Entrust::can('editar_oc')}}";
        var permisoEliminar = "{{Entrust::can('eliminar_oc')}}";
        var permisoImprimir = "{{Entrust::can('imprimir_oc')}}";

    </script>

    <script type="text/javascript" src="/js/views/listPurchaseOrders.js"></script>

@endsection