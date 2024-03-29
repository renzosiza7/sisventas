<div class="sidebar">

    <nav class="sidebar-nav">

        <ul class="nav">

            <li @click="menu = 0" class="nav-item">

                <a class="nav-link active" href="#"><i class="icon-speedometer"></i> Escritorio</a>

            </li>

            <li class="nav-title">

                MENU

            </li>                        

            <li class="nav-item nav-dropdown">

                <a class="nav-link nav-dropdown-toggle" href="#"><i class="icon-basket"></i> Ventas</a>

                <ul class="nav-dropdown-items">

                    <li @click="cambiarMenu('articulos')" class="nav-item">

                        <a class="nav-link" href="#"><i class="icon-list"></i> Artículos</a>

                    </li> 

                    <li @click="cambiarMenu('clientes')" class="nav-item">

                        <a class="nav-link" href="#"><i class="icon-notebook"></i> Clientes</a>

                    </li>

                    <li @click="cambiarMenu('ventas')" class="nav-item">

                        <a class="nav-link" href="#"><i class="icon-basket-loaded"></i> Ventas</a>

                    </li>                    

                    <li @click="cambiarMenu('servicios')" class="nav-item">

                        <a class="nav-link" href="#"><i class="icon-wallet"></i> Servicios</a>

                    </li>  

                </ul>

            </li>                       

            <li class="nav-item nav-dropdown">

                <a class="nav-link nav-dropdown-toggle" href="#"><i class="icon-pie-chart"></i> Reportes</a>

                <ul class="nav-dropdown-items">                    

                    <li @click="cambiarMenu('consulta_venta')" class="nav-item">

                        <a class="nav-link" href="#"><i class="icon-chart"></i> Reporte Ventas</a>

                    </li>

                </ul>

            </li>

            <li @click="menu = 11" class="nav-item">

                <a class="nav-link" href="#"><i class="icon-book-open"></i> Ayuda <span class="badge badge-danger">PDF</span></a>

            </li>

            <li @click="menu = 12" class="nav-item">

                <a class="nav-link" href="#"><i class="icon-info"></i> Acerca de...<span class="badge badge-info">IT</span></a>

            </li>

        </ul>

    </nav>

    <button class="sidebar-minimizer brand-minimizer" type="button"></button>

</div>