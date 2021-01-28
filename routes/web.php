<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes();

Route::group(['middleware'=>['guest']],function(){

    Route::get('/', function () { //mostrar el login
        if(Auth::check()){
            return view('contenido');
        }

        return view('auth.login');
    });

});

Route::group(['middleware'=>['auth']],function(){

    Route::get('/dashboard', 'DashboardController');
    Route::post('/notification/get', 'NotificationController@get');

    Route::get('/main', function () { // interfaz de inicio del sistema

        if(Auth::check()){
            return view('contenido');
        }

        return view('auth.login');

    })->name('main');

    Route::group(['middleware' => ['Almacenero']], function () {

        /**
         * Categorias
         */
        Route::get('/categoria', 'CategoriaController@index');
        Route::post('/categoria/registrar', 'CategoriaController@store');
        Route::put('/categoria/actualizar/{id}', 'CategoriaController@update');
        Route::put('/categoria/activar/{id}', 'CategoriaController@activar');
        Route::put('/categoria/desactivar/{id}', 'CategoriaController@desactivar');
        Route::get('/categoria/select_categoria', 'CategoriaController@getCategoriasActivas');
        Route::get('/categoria/listarPdf','CategoriaController@listarPdf')->name('categorias_pdf');

        /**
         * Marcas
         */
        Route::get('/marca', 'MarcaController@index');
        Route::post('/marca/registrar', 'MarcaController@store');
        Route::put('/marca/actualizar/{id}', 'MarcaController@update');
        Route::put('/marca/activar/{id}', 'MarcaController@activar');
        Route::put('/marca/desactivar/{id}', 'MarcaController@desactivar');
        Route::get('/marca/select_marca', 'MarcaController@getMarcasActivas');
        Route::get('/marca/listarPdf','MarcaController@listarPdf')->name('marcas_pdf');

        /**
         * Artículos
         */
        Route::get('/articulo', 'ArticuloController@index');
        Route::post('/articulo/registrar', 'ArticuloController@store');
        Route::put('/articulo/actualizar/{id}', 'ArticuloController@update');
        Route::put('/articulo/activar/{id}', 'ArticuloController@activar');
        Route::put('/articulo/desactivar/{id}', 'ArticuloController@desactivar');
        Route::get('/articulo/buscarArticulo', 'ArticuloController@buscarArticulo');
        Route::get('/articulo/listarArticulo', 'ArticuloController@listarArticulo');
        Route::get('/articulo/listarPdf','ArticuloController@listarPdf')->name('articulos_pdf');

        /**
         * Proveedores
         */
        Route::get('/proveedor', 'ProveedorController@index');
        Route::post('/proveedor/registrar', 'ProveedorController@store');
        Route::put('/proveedor/actualizar/{id}', 'ProveedorController@update');
        Route::get('/proveedor/selectProveedor', 'ProveedorController@selectProveedor');
        Route::get('/proveedor/listarPdf','ProveedorController@listarPdf')->name('proveedores_pdf');

        /**
         * Ingresos
         */        
        Route::get('/ingreso', 'IngresoController@index');
        Route::post('/ingreso/registrar', 'IngresoController@store');
        Route::put('/ingreso/desactivar/{id}', 'IngresoController@desactivar');
        Route::get('/ingreso/obtenerCabecera', 'IngresoController@obtenerCabecera');
        Route::get('/ingreso/obtenerDetalles', 'IngresoController@obtenerDetalles');
        Route::get('/ingreso/pdf/{id}','IngresoController@pdf')->name('ingreso_pdf');
        Route::get('/ingreso/listarPdf','IngresoController@listarPdf')->name('ingresos_pdf');
    });

    Route::group(['middleware' => ['Vendedor']], function () {

        /**
         * Artículos
         */
        Route::get('/articulo/buscarArticuloVenta', 'ArticuloController@buscarArticuloVenta');
        Route::get('/articulo/listarArticuloVenta', 'ArticuloController@listarArticuloVenta');

        /**
         * Clientes
         */
        Route::get('/cliente', 'ClienteController@index');
        Route::post('/cliente/registrar', 'ClienteController@store');
        Route::put('/cliente/actualizar/{id}', 'ClienteController@update');
        Route::get('/cliente/selectCliente', 'ClienteController@selectCliente');
        Route::get('/cliente/listarPdf','ClienteController@listarPdf')->name('clientes_pdf');

        /**
         * Ventas
         */
        Route::get('/venta', 'VentaController@index');
        Route::get('/venta_caja', 'VentaController@getVentasCaja');
        Route::get('/venta_caja_cerrada', 'VentaController@getVentasCajaCerrada');
        Route::post('/venta/registrar', 'VentaController@store');
        Route::put('/venta/registrar_caja', 'VentaController@registrar_venta_caja'); 
        Route::put('/venta/actualizar_venta_caja/{id}', 'VentaController@actualizar_venta_caja');       
        Route::put('/venta/desactivar/{id}', 'VentaController@desactivar');
        Route::get('/venta/obtenerCabecera', 'VentaController@obtenerCabecera');
        Route::get('/venta/obtenerDetalles', 'VentaController@obtenerDetalles');
        Route::get('/venta/pdf/{id}','VentaController@pdf')->name('venta_pdf');
        Route::get('/venta/pdfTicket/{id}','VentaController@pdfTicket')->name('ventaticket_pdf');
        Route::get('/venta/listarPdf','VentaController@listarPdf')->name('ventas_pdf');

        /**
         * Servicios
         */
        Route::get('/servicio', 'ServicioController@index');
        Route::get('/servicio_caja', 'ServicioController@getServiciosCaja');
        Route::get('/servicio_caja_cerrada', 'ServicioController@getServiciosCajaCerrada');
        Route::post('/servicio/registrar', 'ServicioController@store');
        Route::put('/servicio/registrar_caja', 'ServicioController@registrar_servicio_caja'); 
        Route::put('/servicio/actualizar_servicio_caja/{id}', 'ServicioController@actualizar_servicio_caja');       
        Route::put('/servicio/desactivar/{id}', 'ServicioController@desactivar');
        Route::get('/servicio/obtenerCabecera', 'ServicioController@obtenerCabecera');
        Route::get('/servicio/obtenerDetalles', 'ServicioController@obtenerDetalles');
        Route::get('/servicio/pdf/{id}','ServicioController@pdf')->name('servicio_pdf');
        Route::get('/servicio/pdfTicket/{id}','ServicioController@pdfTicket')->name('servicioticket_pdf');
        Route::get('/servicio/listarPdf','ServicioController@listarPdf')->name('servicios_pdf');    
        

    });

    Route::group(['middleware' => ['Cajero']], function () {       

        /**
         * Clientes
         */
        Route::get('/cliente', 'ClienteController@index');
        Route::post('/cliente/registrar', 'ClienteController@store');
        Route::put('/cliente/actualizar/{id}', 'ClienteController@update');
        Route::get('/cliente/selectCliente', 'ClienteController@selectCliente');
        Route::get('/cliente/listarPdf','ClienteController@listarPdf')->name('clientes_pdf');

        /**
         * Ventas
         */
        Route::get('/venta', 'VentaController@index');
        Route::get('/venta_caja', 'VentaController@getVentasCaja');
        Route::get('/venta_caja_cerrada', 'VentaController@getVentasCajaCerrada');
        Route::post('/venta/registrar', 'VentaController@store');
        Route::put('/venta/registrar_caja', 'VentaController@registrar_venta_caja'); 
        Route::put('/venta/actualizar_venta_caja/{id}', 'VentaController@actualizar_venta_caja');       
        Route::put('/venta/desactivar/{id}', 'VentaController@desactivar');
        Route::get('/venta/obtenerCabecera', 'VentaController@obtenerCabecera');
        Route::get('/venta/obtenerDetalles', 'VentaController@obtenerDetalles');
        Route::get('/venta/pdf/{id}','VentaController@pdf')->name('venta_pdf');
        Route::get('/venta/pdfTicket/{id}','VentaController@pdfTicket')->name('ventaticket_pdf');
        Route::get('/venta/listarPdf','VentaController@listarPdf')->name('ventas_pdf');

        
        /**
         * Caja
         */
        Route::get('/caja', 'CajaController@index');
        Route::get('/caja/get_data', 'CajaController@getDataCaja');
        Route::get('/caja/get_cajeros', 'CajaController@getCajeros');
        Route::post('/caja/registrar', 'CajaController@store');
        Route::put('/caja/actualizar/{id}', 'CajaController@update');
        Route::put('/caja/cerrar/{id}', 'CajaController@close');        

        /**
         * Movimientos
         */
        Route::get('/movimiento', 'MovimientoController@index');
        Route::post('/movimiento/registrar', 'MovimientoController@store');
        Route::put('/movimiento/actualizar/{id}', 'MovimientoController@update');
        Route::delete('/movimiento/eliminar/{id}', 'MovimientoController@destroy');

        /**
         * Gastos
         */
        Route::get('/gasto', 'GastoController@index');
        Route::post('/gasto/registrar', 'GastoController@store');
        Route::put('/gasto/actualizar/{id}', 'GastoController@update');
        Route::delete('/gasto/eliminar/{id}', 'GastoController@destroy');
        Route::get('/gasto/select_tipo_gasto', 'TipoGastoController@getTipoGastosActivos');

        /**
         * Tipo de gastos
         */
        Route::get('/tipo_gasto', 'TipoGastoController@index');
        Route::post('/tipo_gasto/registrar', 'TipoGastoController@store');
        Route::put('/tipo_gasto/actualizar/{id}', 'TipoGastoController@update');
        Route::put('/tipo_gasto/activar/{id}', 'TipoGastoController@activar');
        Route::put('/tipo_gasto/desactivar/{id}', 'TipoGastoController@desactivar'); 

        /**
         * Servicios
         */
        Route::get('/servicio', 'ServicioController@index');
        Route::get('/servicio_caja', 'ServicioController@getServiciosCaja');
        Route::get('/servicio_caja_cerrada', 'ServicioController@getServiciosCajaCerrada');
        Route::post('/servicio/registrar', 'ServicioController@store');
        Route::put('/servicio/registrar_caja', 'ServicioController@registrar_servicio_caja'); 
        Route::put('/servicio/actualizar_servicio_caja/{id}', 'ServicioController@actualizar_servicio_caja');       
        Route::put('/servicio/desactivar/{id}', 'ServicioController@desactivar');
        Route::get('/servicio/obtenerCabecera', 'ServicioController@obtenerCabecera');
        Route::get('/servicio/obtenerDetalles', 'ServicioController@obtenerDetalles');
        Route::get('/servicio/pdf/{id}','ServicioController@pdf')->name('servicio_pdf');
        Route::get('/servicio/pdfTicket/{id}','ServicioController@pdfTicket')->name('servicioticket_pdf');
        Route::get('/servicio/listarPdf','ServicioController@listarPdf')->name('servicios_pdf');  
        
    });
    
    Route::group(['middleware' => ['Administrador']], function () {
        /**
         * Categorias
         */
        Route::get('/categoria', 'CategoriaController@index');
        Route::post('/categoria/registrar', 'CategoriaController@store');
        Route::put('/categoria/actualizar/{id}', 'CategoriaController@update');
        Route::put('/categoria/activar/{id}', 'CategoriaController@activar');
        Route::put('/categoria/desactivar/{id}', 'CategoriaController@desactivar');
        Route::get('/categoria/select_categoria', 'CategoriaController@getCategoriasActivas');
        Route::get('/categoria/listarPdf','CategoriaController@listarPdf')->name('categorias_pdf');

        //Route::get('/facturacion/generar_xml','CategoriaController@generar_xml')->name('generar_xml');
        Route::get('/facturacion/generar_xml', function () {
            return view('facturacion.factura');
        });

        /**
         * Marcas
         */
        Route::get('/marca', 'MarcaController@index');
        Route::post('/marca/registrar', 'MarcaController@store');
        Route::put('/marca/actualizar/{id}', 'MarcaController@update');
        Route::put('/marca/activar/{id}', 'MarcaController@activar');
        Route::put('/marca/desactivar/{id}', 'MarcaController@desactivar');
        Route::get('/marca/select_marca', 'MarcaController@getMarcasActivas');
        Route::get('/marca/listarPdf','MarcaController@listarPdf')->name('marcas_pdf');

        /**
         * Artículos
         */
        Route::get('/articulo', 'ArticuloController@index');
        Route::post('/articulo/registrar', 'ArticuloController@store');
        Route::post('/articulo/actualizar/{id}', 'ArticuloController@update');
        Route::put('/articulo/activar/{id}', 'ArticuloController@activar');
        Route::put('/articulo/desactivar/{id}', 'ArticuloController@desactivar');
        Route::get('/articulo/buscarArticulo', 'ArticuloController@buscarArticulo');
        Route::get('/articulo/buscarArticuloVenta', 'ArticuloController@buscarArticuloVenta');
        Route::get('/articulo/listarArticulo', 'ArticuloController@listarArticulo');
        Route::get('/articulo/listarArticuloVenta', 'ArticuloController@listarArticuloVenta');
        Route::get('/articulo/listarPdf','ArticuloController@listarPdf')->name('articulos_pdf');

        
        /**
         * Proveedores
         */
        Route::get('/proveedor', 'ProveedorController@index');
        Route::post('/proveedor/registrar', 'ProveedorController@store');
        Route::put('/proveedor/actualizar/{id}', 'ProveedorController@update');
        Route::get('/proveedor/selectProveedor', 'ProveedorController@selectProveedor');
        Route::get('/proveedor/listarPdf','ProveedorController@listarPdf')->name('proveedores_pdf');

        /**
         * Ingresos
         */
        Route::get('/ingreso', 'IngresoController@index');
        Route::post('/ingreso/registrar', 'IngresoController@store');
        Route::put('/ingreso/desactivar/{id}', 'IngresoController@desactivar');
        Route::get('/ingreso/obtenerCabecera', 'IngresoController@obtenerCabecera');
        Route::get('/ingreso/obtenerDetalles', 'IngresoController@obtenerDetalles');
        Route::get('/ingreso/pdf/{id}','IngresoController@pdf')->name('ingreso_pdf');
        Route::get('/ingreso/listarPdf','IngresoController@listarPdf')->name('ingresos_pdf');

        /**
         * Ventas
         */
        Route::get('/venta', 'VentaController@index');
        Route::get('/venta_caja', 'VentaController@getVentasCaja');
        Route::get('/venta_caja_cerrada', 'VentaController@getVentasCajaCerrada');
        Route::post('/venta/registrar', 'VentaController@store');
        Route::put('/venta/registrar_caja', 'VentaController@registrar_venta_caja'); 
        Route::put('/venta/actualizar_venta_caja/{id}', 'VentaController@actualizar_venta_caja');       
        Route::put('/venta/desactivar/{id}', 'VentaController@desactivar');
        Route::get('/venta/obtenerCabecera', 'VentaController@obtenerCabecera');
        Route::get('/venta/obtenerDetalles', 'VentaController@obtenerDetalles');
        Route::get('/venta/pdf/{id}','VentaController@pdf')->name('venta_pdf');
        Route::get('/venta/pdfTicket/{id}','VentaController@pdfTicket')->name('ventaticket_pdf');
        Route::get('/venta/listarPdf','VentaController@listarPdf')->name('ventas_pdf');

        /**
         * Servicios
         */
        Route::get('/servicio', 'ServicioController@index');
        Route::get('/servicio_caja', 'ServicioController@getServiciosCaja');
        Route::get('/servicio_caja_cerrada', 'ServicioController@getServiciosCajaCerrada');
        Route::post('/servicio/registrar', 'ServicioController@store');
        Route::put('/servicio/registrar_caja', 'ServicioController@registrar_servicio_caja'); 
        Route::put('/servicio/actualizar_servicio_caja/{id}', 'ServicioController@actualizar_servicio_caja');       
        Route::put('/servicio/desactivar/{id}', 'ServicioController@desactivar');
        Route::get('/servicio/obtenerCabecera', 'ServicioController@obtenerCabecera');
        Route::get('/servicio/obtenerDetalles', 'ServicioController@obtenerDetalles');
        Route::get('/servicio/pdf/{id}','ServicioController@pdf')->name('servicio_pdf');
        Route::get('/servicio/pdfTicket/{id}','ServicioController@pdfTicket')->name('servicioticket_pdf');
        Route::get('/servicio/listarPdf','ServicioController@listarPdf')->name('servicios_pdf');
        
        /**
         * Clientes
         */
        Route::get('/cliente', 'ClienteController@index');
        Route::post('/cliente/registrar', 'ClienteController@store');
        Route::put('/cliente/actualizar/{id}', 'ClienteController@update');
        Route::get('/cliente/selectCliente', 'ClienteController@selectCliente');
        Route::get('/cliente/listarPdf','ClienteController@listarPdf')->name('clientes_pdf');
        
        /**
         * Roles
         */
        Route::get('/rol', 'RolController@index');
        Route::get('/rol/getRolesActivos', 'RolController@getRolesActivos');

        /**
         * Usuarios
         */
        Route::get('/user', 'UserController@index');
        Route::post('/user/registrar', 'UserController@store');
        Route::put('/user/actualizar/{id}', 'UserController@update');
        Route::put('/user/desactivar/{id}', 'UserController@desactivar');
        Route::put('/user/activar/{id}', 'UserController@activar');    
        Route::get('/user/listarPdf','UserController@listarPdf')->name('usuarios_pdf');

        /**
         * Caja
         */
        Route::get('/caja', 'CajaController@index');
        Route::get('/caja/get_data', 'CajaController@getDataCaja');
        Route::get('/caja/get_cajeros', 'CajaController@getCajeros');
        Route::post('/caja/registrar', 'CajaController@store');
        Route::put('/caja/actualizar/{id}', 'CajaController@update');
        Route::put('/caja/cerrar/{id}', 'CajaController@close');

        /**
         * Movimientos
         */
        Route::get('/movimiento', 'MovimientoController@index');
        Route::post('/movimiento/registrar', 'MovimientoController@store');
        Route::put('/movimiento/actualizar/{id}', 'MovimientoController@update');
        Route::delete('/movimiento/eliminar/{id}', 'MovimientoController@destroy');

        /**
         * Gastos
         */
        Route::get('/gasto', 'GastoController@index');
        Route::post('/gasto/registrar', 'GastoController@store');
        Route::put('/gasto/actualizar/{id}', 'GastoController@update');
        Route::delete('/gasto/eliminar/{id}', 'GastoController@destroy');
        Route::get('/gasto/select_tipo_gasto', 'TipoGastoController@getTipoGastosActivos');

        /**
         * Tipo de gastos
         */
        Route::get('/tipo_gasto', 'TipoGastoController@index');
        Route::post('/tipo_gasto/registrar', 'TipoGastoController@store');
        Route::put('/tipo_gasto/actualizar/{id}', 'TipoGastoController@update');
        Route::put('/tipo_gasto/activar/{id}', 'TipoGastoController@activar');
        Route::put('/tipo_gasto/desactivar/{id}', 'TipoGastoController@desactivar'); 
    });   
});

