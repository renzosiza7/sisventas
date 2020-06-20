<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Caja;
use Auth;   
use DB;

class CajaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (!$request->ajax()) return redirect('/');  
        
        $idrol = Auth::user()->idrol;

        if ($idrol == 4) { //si es cajero
            $idcajero = Auth::user()->id;

            $cajas = DB::TABLE('cajas')     
                ->JOIN('personas', 'cajas.idcajero', '=', 'personas.id')                               
                ->SELECT('cajas.*', 'personas.nombre as nombre_cajero')                                    
                ->WHERE('cajas.idcajero', '=', $idcajero)
                ->WHERE('cajas.estado', '=', 'Abierto')
                ->ORDERBY('cajas.id', 'desc')
                ->GET();
            
            return $cajas;
        }
        else {
            $cajas = DB::TABLE('cajas')                                    
                ->JOIN('personas', 'cajas.idcajero', '=', 'personas.id')                               
                ->SELECT('cajas.*', 'personas.nombre as nombre_cajero')                                    
                ->ORDERBY('cajas.id', 'desc')
                ->GET();
            
            return $cajas;
        }              
    }     

    public function getDataCaja(Request $request)
    {
        if (!$request->ajax()) return redirect('/');        

        $idcaja = $request->idcaja;

        $result = array();

        $inicio_caja = DB::TABLE('cajas')                                                                                           
                            ->WHERE('id', '=', $idcaja)
                            ->SELECT('monto_inicial', 'saldo_sistema', 'saldo_caja', 'diferencia', 'estado')
                            ->FIRST();

        $total_ventas = DB::TABLE('ventas')                                                                                           
                ->WHERE('idcaja', '=', $idcaja)
                ->SUM('total');
        
        $total_servicios = DB::TABLE('servicios')                                                                                           
                ->WHERE('idcaja', '=', $idcaja)
                ->SUM('total');
        
        $total_mov_entrada = DB::TABLE('movimientos')                                                                                           
                ->WHERE('tipo', '=', 'Entrada')
                ->WHERE('idcaja', '=', $idcaja)
                ->SUM('monto');

        $total_mov_salida = DB::TABLE('movimientos')                                                                                           
                ->WHERE('tipo', '=', 'Salida')
                ->WHERE('idcaja', '=', $idcaja)
                ->SUM('monto');        
        
        $total_gastos = DB::TABLE('gastos')                                                                                                           
                ->WHERE('idcaja', '=', $idcaja)
                ->SUM('importe');        
        
        $result['inicio_caja'] = $inicio_caja;
        $result['total_ventas'] = $total_ventas;
        $result['total_servicios'] = $total_servicios;
        $result['total_mov_entrada'] = $total_mov_entrada;
        $result['total_mov_salida'] = $total_mov_salida;
        $result['total_gastos'] = $total_gastos;
        
        return $result;
    }    

    public function getCajeros(Request $request)
    {
        if (!$request->ajax()) return redirect('/');        

        $cajeros = DB::TABLE('personas')                        
                ->JOIN('users', 'personas.id', '=', 'users.id')
                ->SELECT('personas.id', 'personas.nombre')                                    
                ->WHERE('users.idrol', '=', 4) // solo cajeros
                ->ORWHERE('users.idrol', '=', 1) // administradores
                ->ORDERBY('personas.nombre', 'asc')
                ->GET();
        
        return $cajeros;
    }    
    

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!$request->ajax()) return redirect('/');

        $this->validate($request, [
            'monto_inicial' => 'required|numeric', 
            'idcajero' => 'required'                                                                                         
        ],
        [
            'idcajero.required' => 'El campo cajero es obligatorio.',                                            
        ]);
        
        $caja = new Caja();

        $caja->monto_inicial = $request->monto_inicial;        
        $caja->estado = "Abierto";
        $caja->idcajero = $request->idcajero;        
        
        $caja->save();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (!$request->ajax()) return redirect('/');
        
        $this->validate($request, [
            'monto_inicial' => 'required|numeric',            
        ]);
        
        $caja = Caja::findOrFail($id);
        
        $caja->monto_inicial = $request->monto_inicial;              
        
        $caja->save();
    }

    public function close(Request $request, $id)
    {
        if (!$request->ajax()) return redirect('/');
                
        $caja = Caja::findOrFail($id);        
        
        $caja->total_ventas = $request->total_ventas;              
        $caja->total_servicios = $request->total_servicios;              
        $caja->total_mov_entrada = $request->total_mov_entrada;              
        $caja->total_mov_salida = $request->total_mov_salida;              
        $caja->total_gastos = $request->total_gastos;
        $caja->saldo_sistema = $request->saldo_sistema;
        $caja->saldo_caja = $request->saldo_caja;
        $caja->diferencia = $request->diferencia;              
        $caja->estado = "Cerrado";              

        $caja->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
       
    }    
}
