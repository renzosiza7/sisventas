<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Marca;

class MarcaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        
        return Marca::all();
    }

    public function getMarcasActivas(Request $request) 
    {
        if (!$request->ajax()) return redirect('/');
        
        $marca = new Marca();

        return $marca->getMarcasActivas();
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
        
        $marca = new Marca();

        $marca->nombre = $request->nombre;
        $marca->descripcion = $request->descripcion;
        $marca->acceso = $request->acceso;
        
        if ($marca->acceso == 'publico') {
            $img_decoded = base64_decode($request->imagen);
            $exploded = explode("/", $request->tipo);

            if ($exploded[1] == 'jpeg') {
                $extension = 'jpg';
            }              

            $filename = str_random() . '.' . $extension;
            $path = public_path() . '/img/marcas/' . $filename;

            file_put_contents($path, $img_decoded);

            $marca->imagen = $filename;                   
        }
        
        $marca->save();
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
        
        $marca = Marca::findOrFail($id);

        $marca->nombre = $request->nombre;
        $marca->descripcion = $request->descripcion;  
        $marca->acceso = $request->acceso;

        if ($marca->imagen != null) {
            $prev_img = public_path() . '/img/marcas/' . $marca->imagen;
            unlink($prev_img); //eliminar la imagen anterior
            $marca->imagen = null;
        }

        if ($marca->acceso == 'publico') {
            $img_decoded = base64_decode($request->imagen);
            $exploded = explode("/", $request->tipo);

            if ($exploded[1] == 'jpeg') {
                $extension = 'jpg';
            }              

            $filename = str_random() . '.' . $extension;
            $path = public_path() . '/img/marcas/' . $filename;

            file_put_contents($path, $img_decoded);

            $marca->imagen = $filename;                   
        }      
        
        $marca->save();
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

        $marca = Marca::findOrFail($id);        
        $marca->condicion = '1';
        
        $marca->save();
    }

    public function desactivar(Request $request, $id) 
    {
        if (!$request->ajax()) return redirect('/');
        
        $marca = Marca::findOrFail($id);        
        $marca->condicion = '0';
        
        $marca->save();
    }

    public function listarPdf(){

        $marca = new Marca();

        $marcas = json_decode($marca->listarPdf());
        
        $cont=Marca::count();

        $pdf = \PDF::loadView('pdf.marcaspdf',['marcas'=>$marcas,'cont'=>$cont]);        
        $pdf->setPaper('A4', 'portrait');        
        
        return $pdf->stream("marcas.pdf", array("Attachment" => false));
    }
}
