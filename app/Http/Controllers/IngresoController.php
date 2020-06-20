<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Ingreso;
use App\Models\DetalleIngreso;
use App\User;
use App\Notifications\NotifyAdmin;
use DB;

class IngresoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        
        $ingreso = new Ingreso();        

        return $ingreso->getIngresos();  
    }

    public function obtenerCabecera(Request $request)
    {
        if (!$request->ajax()) return redirect('/');

        $id = $request->id;

        $ingreso = new Ingreso();        

        return $ingreso->obtenerCabecera($id);        
    }

    public function obtenerDetalles(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        
        $detalle_ingreso = new DetalleIngreso();        
        $id = $request->id;

        return $detalle_ingreso->obtenerDetalles($id);                
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
            'idproveedor' => 'required',
            'tipo_comprobante' => 'required',
            'serie_comprobante' => 'nullable|max:7',
            'num_comprobante' => 'required|max:10',
            'impuesto' => 'required|numeric|between:0, 0.99',      
            'data' => 'array|min:1',      
        ],
        [
            'idproveedor.required' => 'El campo proveedor es obligatorio.',                        
            'num_comprobante.required' => 'El campo nro. comprobante es obligatorio.',                        
            'serie_comprobante.max' => 'El campo serie comprobante no debe ser mayor a 7 digítos.',                        
            'num_comprobante.max' => 'El campo nro. comprobante no debe ser mayor a 10 digítos.',                        
            'impuesto.numeric' => 'El campo impuesto debe ser un número decimal.',
            'data.min' => 'Seleccione por lo menos un artículo para detalles del ingreso.',            
        ]);

        try{
            DB::beginTransaction();

            $mytime= Carbon::now('America/Lima');

            $ingreso = new Ingreso();
            
            $ingreso->idproveedor = $request->idproveedor;
            $ingreso->idusuario = \Auth::user()->id;
            $ingreso->tipo_comprobante = $request->tipo_comprobante;
            $ingreso->serie_comprobante = $request->serie_comprobante;
            $ingreso->num_comprobante = $request->num_comprobante;
            $ingreso->fecha_hora = $mytime->format('Y-m-d H:i:s');
            $ingreso->impuesto = $request->impuesto;
            $ingreso->total = $request->total;
            $ingreso->estado = 'Registrado';
            
            $ingreso->save();

            $detalles = $request->data;//Array de detalles
            //Recorro todos los elementos

            foreach($detalles as $ep=>$det)
            {
                $detalle = new DetalleIngreso();

                $detalle->idingreso = $ingreso->id;
                $detalle->idarticulo = $det['idarticulo'];
                $detalle->cantidad = $det['cantidad'];
                $detalle->precio = $det['precio'];          
                
                $detalle->save();
            }
            
            $fechaActual = date('Y-m-d');

            $numVentas = DB::table('ventas')->whereDate('created_at', $fechaActual)->count();
            $numIngresos = DB::table('ingresos')->whereDate('created_at', $fechaActual)->count();

            $arregloDatos = [
                'ventas' => [
                    'numero' => $numVentas,
                    'msj' => 'Ventas',
                ],
                'ingresos' => [
                    'numero' => $numIngresos,
                    'msj' => 'Ingresos',
                ]
            ];

            $allUsers = User::all();

            foreach($allUsers as $notificar) {
                User::findOrFail($notificar->id)->notify(new NotifyAdmin($arregloDatos));
            }

            DB::commit();
            return [
                'id' => $ingreso->id
            ];

        } catch (Exception $e){
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
        
        $ingreso = Ingreso::findOrFail($id);
        $ingreso->estado = 'Anulado';

        $ingreso->save();
    }

    public function listarPdf(){
        $ingresos = Ingreso::join('personas','ingresos.idproveedor','=','personas.id')
            ->join('users','ingresos.idusuario','=','users.id')
            ->select('ingresos.id','ingresos.tipo_comprobante','ingresos.serie_comprobante',
            'ingresos.num_comprobante','ingresos.fecha_hora','ingresos.impuesto','ingresos.total',
            'ingresos.estado','personas.nombre','users.usuario')
            ->orderBy('ingresos.id', 'asc')->take(100)->get();
        
        $cont=Ingreso::count();

        if ($cont < 100) {
            $cont = Ingreso::count();
        }
        else {
            $cont = 100;
        }

        $pdf = \PDF::loadView('pdf.ingresospdf',['ingresos'=>$ingresos,'cont'=>$cont])->setPaper('a4', 'landscape');
        
        return $pdf->stream('ingresos.pdf', array("Attachment" => false));  
    }

    public function pdf(Request $request,$id){
        $ingreso = Ingreso::join('personas','ingresos.idproveedor','=','personas.id')
        ->join('users','ingresos.idusuario','=','users.id')
        ->select('ingresos.id','ingresos.tipo_comprobante','ingresos.serie_comprobante',
        'ingresos.num_comprobante','ingresos.fecha_hora','ingresos.impuesto','ingresos.total',
        'ingresos.estado','personas.nombre','personas.tipo_documento','personas.num_documento',
        'personas.direccion','personas.email',
        'personas.telefono','users.usuario')
        ->where('ingresos.id','=',$id)
        ->orderBy('ingresos.id', 'desc')->take(1)->get();

        $detalles = DetalleIngreso::join('articulos','detalle_ingresos.idarticulo','=','articulos.id')
        ->select('detalle_ingresos.cantidad','detalle_ingresos.precio',
        'articulos.nombre as articulo')
        ->where('detalle_ingresos.idingreso','=',$id)
        ->orderBy('detalle_ingresos.id', 'desc')->get();

        $numingreso=Ingreso::select('num_comprobante')->where('id',$id)->get();

        $pdf = \PDF::loadView('pdf.ingreso',['ingreso'=>$ingreso,'detalles'=>$detalles]);        

        return $pdf->stream('venta-'.$numingreso[0]->num_comprobante.'.pdf', array("Attachment" => false));
    }
}
