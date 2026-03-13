<ul id="mainnav-menu" class="list-group">

    <!--Menu list item-->
    <li class="{{ isActiveURL('home', 'active-link') }}">
        <a href="{!! url('home') !!}">
            <i class="pli-home"></i>
            <span class="menu-title">Dashboard</span>
        </a>
    </li>
    <li class="list-divider"></li>
    <!--Category name-->
    <li class="list-header">Módulos</li>

    <li class="{{ isActiveURL('orden-despacho', 'active-link') }}">
        <a href="{!! url('orden-despacho') !!}">
            <i class="pli-mail-send"></i>
            <span class="menu-title">
                <span class="menu-title">Envíos</span>
            </span>
        </a>
    </li>
    <li class="{{ isActiveURL('embarque', 'active-link') }}">
        <a href="{!! url('embarque') !!}">
            <i class="pli-ship-2"></i>
            <span class="menu-title">
                <span class="menu-title">Embarques</span>
            </span>
        </a>
    </li>
    <li>
        <a href="#">
            <i class="pli-board"></i>
            <span class="menu-title">
                <span class="menu-title">Reportes</span>
            </span>
        </a>
    </li>

    <li class="list-divider"></li>

    <!--Category name-->
    <li class="list-header">Sistema</li>



    <!--Menu list item-->
    <li class="{{ isActiveURL('cliente', 'active-link') }}">
        <a href="{!! url('cliente') !!}">
            <i class="pli-business-man"></i>
            <span class="menu-title">
                <span class="menu-title">Clientes</span>
            </span>
        </a>
    </li>
    <li class="{{ isActiveURL('cat-producto', 'active-link') }}">
        <a href="{!! url('cat-producto') !!}">
            <i class="pli-presents"></i>
            <span class="menu-title">
                <span class="menu-title">Productos</span>
            </span>
        </a>
    </li>
    <li class="{{ isActiveURL('tipo-producto', 'active-link') }}">
        <a href="{!! url('tipo-producto') !!}">
            <i class="pli-tag"></i>
            <span class="menu-title">
                <span class="menu-title">Categorías</span>
            </span>
        </a>
    </li>
    <li class="{{ isActiveURL('usuario', 'active-link') }}">
        <a href="{!! url('usuario') !!}">
            <i class="pli-mens"></i>
            <span class="menu-title">
                <span class="menu-title">Usuarios</span>
            </span>
        </a>
    </li>

    <li class="{{ isActiveURL('settings', 'active-link') }}">
        <a href="{!! url('settings') !!}">
            <i class="pli-gear"></i>
            <span class="menu-title">
                <span class="menu-title">Configuración</span>
            </span>
        </a>
    </li>
    <hr>

    <br>

    <br>

    <br>
</ul>
