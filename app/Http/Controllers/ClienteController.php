<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Persona;

class ClienteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (!$request->ajax()) return redirect('/');

        return Persona::all();
    }

    public function selectCliente(Request $request)
    {       
        if (!$request->ajax()) return redirect('/');

        $filtro = $request->filtro;

        $persona = new Persona();        

        return $persona->selectCliente($filtro);
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
        ],
        [                        
            'num_documento.max' => 'El campo nro. documento no debe ser mayor a 20 dígitos.',                        
        ]);
        
        $persona = new Persona();
        $persona->nombre = $request->nombre;
        $persona->tipo_documento = $request->tipo_documento;
        $persona->num_documento = $request->num_documento;
        $persona->direccion = $request->direccion;
        $persona->telefono = $request->telefono;
        $persona->email = $request->email;

        $persona->save();
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
        ],
        [                        
            'num_documento.max' => 'El campo nro. documento no debe ser mayor a 20 dígitos.',                        
        ]);
        
        $persona = Persona::findOrFail($id);
        $persona->nombre = $request->nombre;
        $persona->tipo_documento = $request->tipo_documento;
        $persona->num_documento = $request->num_documento;
        $persona->direccion = $request->direccion;
        $persona->telefono = $request->telefono;
        $persona->email = $request->email;
        
        $persona->save();
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
        $personas = Persona::orderBy('personas.nombre', 'asc')->get();        
        $cont=Persona::count();

        $pdf = \PDF::loadView('pdf.clientespdf',['clientes'=>$personas,'cont'=>$cont]);
        $pdf->setPaper('A4', 'portrait');        
        
        return $pdf->stream("clientes.pdf", array("Attachment" => false));    
    }
}
