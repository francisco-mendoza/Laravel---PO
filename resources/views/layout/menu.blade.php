<!-- Main navigation -->
<div class="sidebar-category sidebar-category-visible">
    <div class="category-content no-padding">
        <ul class="navigation navigation-main navigation-accordion">

            <!-- Main -->
            <li class="navigation-header"><span>Menu</span> <i class="icon-menu" title="Main pages"></i></li>
            <?php $arregloMenu = array();?>
            <?php $existeTituloFactura = false;?>
            <?php $colocarFinalFactura = false;?>
            @foreach($menu_opciones as $menu)
                <?php $opcion = 'ver_'. $menu->option_route; ?>
                @permission($opcion)
                    @if($menu->order_option == 4)
                        <li class="navigation-header">
                            <span>Administración</span>
                            <i class="icon-menu" title="Administración"></i>
                        </li>
                    @endif
                    @if($menu->order_option == 7)
                        @if(!$existeTituloFactura)
                            <li class="opcion_menu" id="facturas" >
                                <a href=""><i class="icon-printer2"></i>
                                    <span>Facturas</span>
                                </a>
                                <ul>
                            <?php $existeTituloFactura = true;?>
                            <?php $colocarFinalFactura = true;?>
                        @endif
                        <li class="opcion_menu" id="{{$menu->id_menu}}" data-role="{{$menu->option_route}}">
                            <a href="{{route($menu->option_route)}}"><i class="{{$menu->option_icon}}"></i>
                                <span>{{$menu->name_option}}</span>
                            </a>
                        </li>

                    @else
                        @if($colocarFinalFactura)
                            <?php $colocarFinalFactura = false;?>
                                </ul>
                            </li>
                        @endif
                        <li class="opcion_menu" id="{{$menu->id_menu}}" data-role="{{$menu->option_route}}">
                            <a href="{{route($menu->option_route)}}"><i class="{{$menu->option_icon}}"></i>
                                <span>{{$menu->name_option}}</span>
                            </a>
                        </li>
                    @endif
                @endpermission
            @endforeach

        </ul>
    </div>
</div>
<!-- /main navigation -->