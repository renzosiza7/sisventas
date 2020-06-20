<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Gasto;
use DB;

class GastoController extends Controller
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

        $gastos = DB::TABLE('gastos')                        
            ->JOIN('tipo_gastos', 'gastos.idtipo_gasto', '=', 'tipo_gastos.id')
            ->SELECT('gastos.id', 'gastos.importe', 'gastos.idtipo_gasto', 'tipo_gastos.nombre as tipo_gasto', 
                     'gastos.created_at', 'gastos.descripcion')                                    
            ->WHERE('gastos.idcaja', '=', $idcaja)
            ->ORDERBY('gastos.id', 'desc')
            ->GET();

        //return Gasto::all();
        return $gastos;
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
            'importe' => 'required|numeric',                                   
            'descripcion' => 'nullable|max:150',          
            'idtipo_gasto' => 'required',                 
        ]);
        
        $gasto = new Gasto();

        $gasto->importe = $request->importe;
        $gasto->descripcion = $request->descripcion;
        $gasto->idtipo_gasto = $request->idtipo_gasto;        
        $gasto->idcaja = $request->idcaja;
        
        $gasto->save();
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
            'importe' => 'required|numeric',                                                                      
            'descripcion' => 'nullable|max:150',            
            'idtipo_gasto' => 'required',    
        ]);
        
        $gasto = Gasto::findOrFail($id);

        $gasto->importe = $request->importe;        
        $gasto->descripcion = $request->descripcion;            
        $gasto->idtipo_gasto = $request->idtipo_gasto;
        
        $gasto->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $gasto = Gasto::findOrFail($id);
        $gasto->delete(); 
    }    
}
