<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Categoria;

class CategoriaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {         
        if (!$request->ajax()) return redirect('/');

        $query = Categoria::where('nombre', 'like', '%' . $request->filter . '%')
                            ->orWhere('descripcion', 'like', '%' . $request->filter . '%');                                

        $sortby = $request->sortby;

        if ($sortby && !empty($sortby)) {                        
            $sortdirection = $request->sortdesc == "true" ? 'desc' : 'asc';
            $query = $query->orderBy($sortby, $sortdirection);
        }
        else {
            $query = $query->latest();
        }        

        return $query->paginate($request->size);
    }

    public function getCategoriasActivas(Request $request) 
    {
        if (!$request->ajax()) return redirect('/');

        $categoria = new Categoria();

        return $categoria->getCategoriasActivas();
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
            'nombre' => 'required|unique:categorias|max:50',            
            'descripcion' => 'nullable|max:150'
        ]);                

        //\Log::info($request->all());       

        $categoria = new Categoria();

        $categoria->nombre = $request->nombre;
        $categoria->descripcion = $request->descripcion;
        $categoria->acceso = $request->acceso;       
        
        $categoria->save();
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
            'nombre' => 'required|max:50|unique:categorias,nombre,' . $id,
            'descripcion' => 'nullable|max:150'
        ]);        
        
        $categoria = Categoria::findOrFail($id);

        $categoria->nombre = $request->nombre;
        $categoria->descripcion = $request->descripcion;
        $categoria->acceso = $request->acceso;             

        $categoria->save();        
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

        $categoria = Categoria::findOrFail($id);        
        $categoria->condicion = '1';
        
        $categoria->save();
    }

    public function desactivar(Request $request, $id) 
    {
        if (!$request->ajax()) return redirect('/');

        $categoria = Categoria::findOrFail($id);        
        $categoria->condicion = '0';
        
        $categoria->save();
    }

    public function listarPdf(){

        $categoria = new Categoria();

        $categorias = json_decode($categoria->listarPdf());
        
        $cont=Categoria::count();

        $pdf = \PDF::loadView('pdf.categoriaspdf',['categorias'=>$categorias,'cont'=>$cont]);        
        $pdf->setPaper('A4', 'portrait');        
        
        return $pdf->stream("categorias.pdf", array("Attachment" => false));
    }
}
