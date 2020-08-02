<?php

namespace App\Http\Controllers;

use File;
use Illuminate\Http\Request;
use App\Models\Categoria;
use App\Models\Marca;
use App\Models\Articulo;

class ArticuloController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        
        if (!$request->ajax()) return redirect('/');

        $articulo = new Articulo();        

        return $articulo->getArticulos();        
    }

    public function buscarArticulo(Request $request) 
    {             
        if (!$request->ajax()) return redirect('/');

        $filtro = $request->filtro;

        $articulo = new Articulo();        

        return $articulo->buscarArticulo($filtro);        
    }

    public function buscarArticuloVenta(Request $request) 
    {     
        if (!$request->ajax()) return redirect('/');

        $filtro = $request->filtro;

        $articulo = new Articulo();        

        return $articulo->buscarArticuloVenta($filtro);        
    }

    public function listarArticulo(Request $request)
    {
        if (!$request->ajax()) return redirect('/');

        $buscar = $request->buscar;
        $criterio = $request->criterio;

        $articulo = new Articulo();        

        return $articulo->listarArticulo($buscar, $criterio);              
    }
    
    public function listarArticuloVenta(Request $request)
    {
        if (!$request->ajax()) return redirect('/');

        $buscar = $request->buscar;
        $criterio = $request->criterio;

        $articulo = new Articulo();        

        return $articulo->listarArticuloVenta($buscar, $criterio);              
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

        //$files = $request->file('imagen');

        //\Log::info($files->getClientOriginalExtension());        
        
        //$img = base64_encode(file_get_contents($_FILES['imagen']['tmp_name']));

        //\Log::info($img);            

        $this->validate($request, [
            'codigo' => 'required|max:50|unique:articulos',
            'nombre' => 'required|max:100',            
            'idcategoria' => 'required',
            'idmarca' => 'required',
            'precio_venta' => 'required|numeric',
            'stock' => 'required|integer',
            'descripcion' => 'nullable|max:256',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ],
        [
            'idcategoria.required' => 'El campo categoría es obligatorio.',            
            'idmarca.required' => 'El campo marca es obligatorio.',            
            'precio_venta.numeric' => 'El campo precio venta debe ser un número decimal.',            
            'stock.integer' => 'El campo stock debe ser un número entero',
            'precio_venta.required' => 'El campo precio venta es obligatorio.',            
        ]);

        $articulo = new Articulo();

        $articulo->codigo = $request->codigo;
        $articulo->nombre = $request->nombre;
        $articulo->idcategoria = $request->idcategoria;
        $articulo->idmarca = $request->idmarca;        
        $articulo->precio_compra = $request->precio_compra;
        $articulo->precio_venta = $request->precio_venta;
        $articulo->stock = $request->stock;
        $articulo->descripcion = $request->descripcion;
        $articulo->acceso = $request->acceso;
        $articulo->condicion = '1';

        if ($files = $request->file('imagen')) {        
            //insert new file
            $destinationPath = public_path() . '/img/articulos/'; // upload path
            $profileImage = date('YmdHis') . "." . $files->getClientOriginalExtension();
            $files->move($destinationPath, $profileImage);
            $articulo->imagen = $profileImage;
        }

        $articulo->save();
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
            'codigo' => 'required|max:50',
            'nombre' => 'required|max:100',            
            'idcategoria' => 'required',
            'idmarca' => 'required',
            'precio_venta' => 'required|numeric',
            'stock' => 'required|integer',
            'descripcion' => 'nullable|max:256'
        ],
        [
            'idcategoria.required' => 'El campo categoría es obligatorio.',            
            'idmarca.required' => 'El campo marca es obligatorio.',            
            'precio_venta.numeric' => 'El campo precio venta debe ser un número decimal.',            
            'stock.integer' => 'El campo stock debe ser un número entero',
            'precio_venta.required' => 'El campo precio venta es obligatorio.',            
        ]);
        
        $articulo = Articulo::findOrFail($id);  
        
        $articulo->codigo = $request->codigo;
        $articulo->nombre = $request->nombre;
        $articulo->idcategoria = $request->idcategoria;
        $articulo->idmarca = $request->idmarca;
        $articulo->precio_compra = $request->precio_compra;
        $articulo->precio_venta = $request->precio_venta;
        $articulo->stock = $request->stock;
        $articulo->descripcion = $request->descripcion;
        $articulo->acceso = $request->acceso;
        $articulo->condicion = '1';

        if ($articulo->acceso == 'privado') {
            //delete old file
            File::delete(public_path() . $request->url_imagen);
            $articulo->imagen = null;
        }

        if ($files = $request->file('imagen')) {             
             
            //insert new file
            $destinationPath = public_path() . '/img/articulos/'; // upload path
            $profileImage = date('YmdHis') . "." . $files->getClientOriginalExtension();
            $files->move($destinationPath, $profileImage);
            $articulo->imagen = $profileImage;
        }

        $articulo->save();
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

        $categoria = Articulo::findOrFail($id);        
        $categoria->condicion = '1';
        
        $categoria->save();
    }

    public function desactivar(Request $request, $id) 
    {
        if (!$request->ajax()) return redirect('/');
        
        $categoria = Articulo::findOrFail($id);        
        $categoria->condicion = '0';
        
        $categoria->save();
    }

    public function listarPdf()
    {             
        $articulo = new Articulo();        

        $articulos = json_decode($articulo->getArticulosPdf());      

        $cont=Articulo::count();        

        $pdf = \PDF::loadView('pdf.articulospdf',
                              [ 'articulos'=>$articulos,
                                'cont'=>$cont
                              ]);

        $pdf->setPaper('A4', 'landscape');
        
        //return $pdf->download('articulos.pdf');
        return $pdf->stream("articulos.pdf", array("Attachment" => false));    
    }
}
