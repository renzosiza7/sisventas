<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Proveedor;
use App\Models\Persona;
use DB;

class ProveedorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (!$request->ajax()) return redirect('/');

        $proveedor = new Proveedor();        

        return $proveedor->getProveedores();  
    }

    public function selectProveedor(Request $request) 
    {        
        if (!$request->ajax()) return redirect('/');

        $filtro = $request->filtro;

        $proveedor = new Proveedor();        

        return $proveedor->selectProveedor($filtro);  
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
            'nombre' => 'required|max:100',            
            'tipo_documento' => 'nullable|max:20',
            'num_documento' => 'nullable|max:20',
            'direccion' => 'nullable|max:70',
            'telefono' => 'nullable|digits_between:6,12',
            'email' => 'nullable|email|max:50',
            'contacto' => 'nullable|max:50',
            'telefono_contacto' => 'nullable|max:50',
        ],
        [                        
            'num_documento.max' => 'El campo nro. documento no debe ser mayor a 20 dígitos.',
            'telefono_contacto.max' => 'El campo telefono contacto no debe ser mayor a 50 dígitos.',                        
        ]);
        
        try {
            DB::beginTransaction();

            $persona = new Persona();

            $persona->nombre = $request->nombre;
            $persona->tipo_documento = $request->tipo_documento;
            $persona->num_documento = $request->num_documento;
            $persona->direccion = $request->direccion;
            $persona->telefono = $request->telefono;
            $persona->email = $request->email;
            
            $persona->save();

            $proveedor = new Proveedor();

            $proveedor->id = $persona->id;
            $proveedor->contacto = $request->contacto;
            $proveedor->telefono_contacto = $request->telefono_contacto;            
            
            $proveedor->save();

            DB::commit();

        } catch (Exception $e) {
            DB::rollBack();
        }
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
            'nombre' => 'required|max:100',            
            'tipo_documento' => 'nullable|max:20',
            'num_documento' => 'nullable|max:20',
            'direccion' => 'nullable|max:70',
            'telefono' => 'nullable|digits_between:6,12',
            'email' => 'nullable|email|max:50',
            'contacto' => 'nullable|max:50',
            'telefono_contacto' => 'nullable|max:50',
        ],
        [                        
            'num_documento.max' => 'El campo nro. documento no debe ser mayor a 20 dígitos.',
            'telefono_contacto.max' => 'El campo telefono contacto no debe ser mayor a 50 dígitos.',                        
        ]);        
        
        try {
            DB::beginTransaction();

            //Buscar primero el proveedor a modificar
            $proveedor = Proveedor::findOrFail($id);

            $persona = Persona::findOrFail($proveedor->id);

            $persona->nombre = $request->nombre;
            $persona->tipo_documento = $request->tipo_documento;
            $persona->num_documento = $request->num_documento;
            $persona->direccion = $request->direccion;
            $persona->telefono = $request->telefono;
            $persona->email = $request->email;

            $persona->save();
            
            $proveedor->contacto = $request->contacto;
            $proveedor->telefono_contacto = $request->telefono_contacto;

            $proveedor->save();

            DB::commit();

        } catch (Exception $e) {
            DB::rollBack();
        }
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

    public function listarPdf(){
        $personas = Proveedor::join('personas','proveedores.id','=','personas.id')
            ->select('personas.id','personas.nombre','personas.tipo_documento',
            'personas.num_documento','personas.direccion','personas.telefono',
            'personas.email','proveedores.contacto','proveedores.telefono_contacto')
            ->orderBy('personas.nombre', 'asc')->get();        
        
        $cont=Proveedor::count();

        $pdf = \PDF::loadView('pdf.proveedorespdf',['proveedores'=>$personas,'cont'=>$cont]);
        $pdf->setPaper('A4', 'landscape');        
        
        return $pdf->stream("proveedores.pdf", array("Attachment" => false));    
    }
}
