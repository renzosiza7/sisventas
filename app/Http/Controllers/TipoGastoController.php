<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TipoGasto;

class TipoGastoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (!$request->ajax()) return redirect('/');

        return TipoGasto::all();
    }

    public function getTipoGastosActivos(Request $request) 
    {
        if (!$request->ajax()) return redirect('/');

        $tipo_gasto = new TipoGasto();

        return $tipo_gasto->getTipoGastoActivos();
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
            'nombre' => 'required|max:50',            
            'descripcion' => 'nullable|max:150'
        ]);
        
        $tipo_gasto = new TipoGasto();

        $tipo_gasto->nombre = $request->nombre;
        $tipo_gasto->descripcion = $request->descripcion;
        
        $tipo_gasto->save();
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
            'nombre' => 'required|max:50',            
            'descripcion' => 'nullable|max:150'
        ]);
        
        $tipo_gasto = TipoGasto::findOrFail($id);

        $tipo_gasto->nombre = $request->nombre;
        $tipo_gasto->descripcion = $request->descripcion;        
        
        $tipo_gasto->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function activar(Request $request, $id) 
    {
        if (!$request->ajax()) return redirect('/');

        $tipo_gasto = TipoGasto::findOrFail($id);        
        $tipo_gasto->condicion = '1';
        
        $tipo_gasto->save();
    }

    public function desactivar(Request $request, $id) 
    {
        if (!$request->ajax()) return redirect('/');

        $tipo_gasto = TipoGasto::findOrFail($id);        
        $tipo_gasto->condicion = '0';
        
        $tipo_gasto->save();
    }   
}
