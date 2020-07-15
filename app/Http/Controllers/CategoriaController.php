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

        return Categoria::all();
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
            'nombre' => 'required|max:50',            
            'descripcion' => 'nullable|max:150'
        ]);                

        //\Log::info($request->all());       

        $categoria = new Categoria();

        $categoria->nombre = $request->nombre;
        $categoria->descripcion = $request->descripcion;
        $categoria->acceso = $request->acceso;
        
        if ($categoria->acceso == 'publico') {           

            $img_decoded = base64_decode($request->imagen);
            $exploded = explode("/", $request->tipo);

            if ($exploded[1] == 'jpeg') {
                $extension = 'jpg';
            }              

            $filename = str_random() . '.' . $extension;
            $path = public_path() . '/img/categorias/' . $filename;

            file_put_contents($path, $img_decoded);

            $categoria->imagen = $filename;                   
        }
        
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
            'nombre' => 'required|max:50',            
            'descripcion' => 'nullable|max:150'
        ]);        
        
        $categoria = Categoria::findOrFail($id);

        $categoria->nombre = $request->nombre;
        $categoria->descripcion = $request->descripcion;
        $categoria->acceso = $request->acceso;

        if ($categoria->imagen != null) {
            $prev_img = public_path() . '/img/categorias/' . $categoria->imagen;
            unlink($prev_img); //eliminar la imagen anterior
            $categoria->imagen = null;
        }

        if ($categoria->acceso == 'publico') {                          

            $img_decoded = base64_decode($request->imagen);
            $exploded = explode("/", $request->tipo);

            if ($exploded[1] == 'jpeg') {
                $extension = 'jpg';
            }              

            $filename = str_random() . '.' . $extension;
            $path = public_path() . '/img/categorias/' . $filename;
            file_put_contents($path, $img_decoded);            

            $categoria->imagen = $filename;                   
        }

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
