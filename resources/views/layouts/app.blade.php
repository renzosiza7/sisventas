<!DOCTYPE html>
<html lang="es">
<head>

  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="Sistema de Ventas">
  <meta name="author" content="RS7-APPS">
  <meta name="keyword" content="sistema ventas, compras, clientes, proveedores">
  <title>Sistema de Ventas</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link href="css/font-awesome.min.css" rel="stylesheet">
  <link href="css/simple-line-icons.min.css" rel="stylesheet">
    <!-- Main styles for this application -->
  <link href="css/style.css" rel="stylesheet">
</head>

<body class="app flex-row align-items-center">
  <div class="container">
    @yield('login')
  </div>
  
  <!-- Bootstrap and necessary plugins -->
  <script src="js/jquery.min.js"></script>   
  <!-- vuejs -->
  <script src="{{ asset(mix('js/app.js')) }}"></script>
  <script src="js/popper.min.js"></script>
  <script src="js/bootstrap.min.js"></script>    
  <script src="js/pace.min.js"></script>
  <!-- Plugins and scripts required by all views -->
  <script src="js/Chart.min.js"></script>
  <!-- GenesisUI main scripts -->
  <script src="js/template.js"></script>   
</body>
</html>
