<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Models\Persona;
use DB;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (!$request->ajax()) return redirect('/');

        $usuario = new User();        

        return $usuario->getUsuarios();  
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
            'usuario' => 'required|max:20',
            'password' => 'required|max:12',
            'idrol' => 'required',
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

            $user = new User();

            $user->id = $persona->id;            
            $user->usuario = $request->usuario;
            $user->password = bcrypt( $request->password);
            $user->condicion = '1';  
            $user->idrol = $request->idrol; 

            $user->save();

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
            'usuario' => 'required|max:20',
            'password' => 'required|max:12',
            'idrol' => 'required',
        ],
        [                        
            'num_documento.max' => 'El campo nro. documento no debe ser mayor a 20 dígitos.',
            'telefono_contacto.max' => 'El campo telefono contacto no debe ser mayor a 50 dígitos.',                        
        ]);            
        
        try {
            DB::beginTransaction();

            $user = User::findOrFail($id);
            $persona = Persona::findOrFail($user->id);

            $persona->nombre = $request->nombre;
            $persona->tipo_documento = $request->tipo_documento;
            $persona->num_documento = $request->num_documento;
            $persona->direccion = $request->direccion;
            $persona->telefono = $request->telefono;
            $persona->email = $request->email;

            $persona->save();

            
            $user->usuario = $request->usuario;
            $user->password = bcrypt( $request->password);            
            $user->idrol = $request->idrol;

            $user->save();

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

    public function desactivar(Request $request, $id)
    {
        if (!$request->ajax()) return redirect('/');
        
        $user = User::findOrFail($id);
        $user->condicion = '0';

        $user->save();
    }

    public function activar(Request $request, $id)
    {        
        if (!$request->ajax()) return redirect('/');
        
        $user = User::findOrFail($id);
        $user->condicion = '1';

        $user->save();
    }

    public function listarPdf(){
        $usuarios = User::join('personas','users.id','=','personas.id')
            ->join('roles','users.idrol','=','roles.id')
            ->select('personas.id','personas.nombre','personas.tipo_documento','personas.num_documento','personas.direccion','personas.telefono','personas.email','users.usuario','users.password','users.condicion','users.idrol','roles.nombre as rol')
            ->orderBy('personas.id', 'desc')->get();     
        $cont=User::count();

        $pdf = \PDF::loadView('pdf.usuariospdf',['usuarios'=>$usuarios,'cont'=>$cont]);
        $pdf->setPaper('A4', 'landscape');        
        
        return $pdf->stream("usuarios.pdf", array("Attachment" => false));  
    }
}
