<!-- Main navbar -->
<div class="navbar navbar-inverse">
    <div class="navbar-header">
        <a href="/">{{ HTML::image("images/logo_yapo.png", "Logo", array('class' => 'logo_title')) }}</a>

        {{link_to_route('home', $title = 'Ordenes Compra', $parameters = [], $attributes = ['class'=>'navbar-brand','style'=>'font-weight: bold'])}}

        <ul class="nav navbar-nav visible-xs-block">
            <li><a data-toggle="collapse" data-target="#navbar-mobile"><i class="icon-tree5"></i></a></li>
            <li><a class="sidebar-mobile-main-toggle"><i class="icon-paragraph-justify3"></i></a></li>
        </ul>
    </div>

    <div class="navbar-collapse collapse" id="navbar-mobile">
        <ul class="nav navbar-nav">
            <li><a class="sidebar-control sidebar-main-toggle hidden-xs"><i class="icon-paragraph-justify3"></i></a></li>
        </ul>

        <p class="navbar-text"><span class="label bg-success">En Línea</span></p>

        <ul class="nav navbar-nav navbar-right">

            <li class="dropdown dropdown-user">
                <a class="dropdown-toggle" data-toggle="dropdown">
                    @if(Auth::check())
                    <img src="{{Auth::user()->url_avatar}}" alt="">
                    <span>{{Auth::user()->username}}</span>
                    @endif
                </a>
            </li>
            <li><a href="{{url('auth/logout')}}"><i class="icon-switch2"></i> Cerrar Sesión</a></li>
        </ul>
    </div>
</div>
<!-- /main navbar -->