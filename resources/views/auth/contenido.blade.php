<!DOCTYPE html>
<html lang="en">
	<head><meta http-equiv="Content-Type" content="text/html; charset=gb18030">
		
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />
		<meta name="description" content="Evaluaci��n en linea para estudiantes" />
		<meta name="keywords" content="Sistema,Evaluación, online, instituto, universidad" />
		<meta name="author" content="RS7" />
		<!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">
		<title>Sistema de Ventas</title>
		<!-- Bootstrap CSS -->
		<link href="css/bootstrap.min.css" rel="stylesheet" media="screen" />
		<!-- Login CSS -->
		<link href="css/main2.css" rel="stylesheet" />
		<!-- Ion Icons -->
		<link href="fonts/icomoon/icomoon.css" rel="stylesheet" />
	</head>
	<body class="login-bg">
		@yield('login')
	</body>
</html>
