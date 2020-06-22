<!DOCTYPE html>

<html lang="es">



<head>

    <meta charset="utf-8">

    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <meta name="description" content="Sistema de Ventas">

    <meta name="author" content="www.rs7.app">

    <meta name="keyword" content="sistema ventas facturacion">    

    <link rel="shortcut icon" href="img/favicon.png">

    <meta name="userId" content="{{ Auth::check() ? Auth::user()->id : '' }}">

    <title>Sistema de Ventas</title>    

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Icons -->

    <link href="css/font-awesome.min.css" rel="stylesheet">

    <link href="css/simple-line-icons.min.css" rel="stylesheet">

    <link href="css/sweetalert2.css" rel="stylesheet">

    <!-- Main styles for this application -->

    <link href="css/style.css" rel="stylesheet">    

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.min.js">

</head>



<body class="app header-fixed sidebar-fixed aside-menu-fixed aside-menu-hidden">

    <div id="app">

    <header class="app-header navbar">

        <button class="navbar-toggler mobile-sidebar-toggler d-lg-none mr-auto" type="button">

          <span class="navbar-toggler-icon"></span>

        </button>

        <a class="navbar-brand" href="#"></a>

        <button class="navbar-toggler sidebar-toggler d-md-down-none" type="button">

          <span class="navbar-toggler-icon"></span>

        </button>

        <!--<ul class="nav navbar-nav d-md-down-none">

            <li class="nav-item px-3">

                <a class="nav-link" href="#">Escritorio</a>

            </li>

            <li class="nav-item px-3">

                <a class="nav-link" href="#">Configuraciones</a>

            </li>

        </ul>-->

        <ul class="nav navbar-nav ml-auto">

            <notification-component :notifications="notifications"></notification-component>            

            <li class="nav-item dropdown">

                <a class="nav-link dropdown-toggle nav-link" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">

                    <img src="img/avatars/9.jpg" class="img-avatar">

                    <span class="d-md-down-none">{{ Auth::user()->usuario }} </span>

                </a>

                <div class="dropdown-menu dropdown-menu-right">

                    <!--<div class="dropdown-header text-center">

                        <strong>Cuenta</strong>

                    </div>

                    <a class="dropdown-item" href="#"><i class="fa fa-user"></i> Perfil</a>-->

                    <a class="dropdown-item" href="{{ route('logout') }}"

                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">

                        <i class="fa fa-sign-out fa-lg" aria-hidden="true"></i> Cerrar sesión

                    </a>

                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">

                        {{ csrf_field() }}

                    </form>

                </div>

            </li>

        </ul>

    </header>



    <div class="app-body">



        @if(Auth::check())

            @if (Auth::user()->idrol == 1)

                @include('sidebar_administrador')

            @elseif (Auth::user()->idrol == 2)

                @include('sidebar_vendedor')

            @elseif (Auth::user()->idrol == 3)

                @include('sidebar_almacenero')

            @elseif (Auth::user()->idrol == 4)

                @include('sidebar_cajero')

            @else

            

            @endif



        @endif        



        <!-- Contenido Principal -->

        @yield('contenido')

        <!-- /Fin del contenido principal -->

    </div>



    </div>



    @include('footer');

    

    

    <!-- Bootstrap and necessary plugins -->

    <script src="js/jquery.min.js"></script>   

    <!-- vuejs -->

    <script src="js/app.js"></script> 

    <script src="js/popper.min.js"></script>

    <script src="js/bootstrap.min.js"></script>    

    <script src="js/pace.min.js"></script>

    <!-- Plugins and scripts required by all views -->

    <script src="js/Chart.min.js"></script>

    <!-- GenesisUI main scripts -->

    <script src="js/template.js"></script>      

    <script src="js/sweetalert2.min.js"></script>

</body>



</html>