<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Movimiento;
use DB;

class MovimientoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (!$request->ajax()) return redirect('/');

        $idcaja = $request->idcaja;

        $movimientos = DB::TABLE('movimientos')                                    
            ->SELECT('movimientos.*')                                    
            ->WHERE('movimientos.idcaja', '=', $idcaja)
            ->ORDERBY('movimientos.id', 'desc')
            ->GET();
        
        return $movimientos;
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
            'monto' => 'required|numeric',                                   
            'tipo' => 'required',                                   
            'descripcion' => 'nullable|max:150'            
        ]);
        
        $movimiento = new Movimiento();

        $movimiento->monto = $request->monto;
        $movimiento->tipo = $request->tipo;
        $movimiento->descripcion = $request->descripcion;
        $movimiento->idcaja = $request->idcaja;
        
        $movimiento->save();
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
            'monto' => 'required|numeric',                          
            'tipo' => 'required',                                            
            'descripcion' => 'nullable|max:150'            
        ]);
        
        $movimiento = Movimiento::findOrFail($id);

        $movimiento->monto = $request->monto;
        $movimiento->tipo = $request->tipo;
        $movimiento->descripcion = $request->descripcion;            
        
        $movimiento->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $movimiento = Movimiento::findOrFail($id);
        $movimiento->delete(); 
    }    
}
