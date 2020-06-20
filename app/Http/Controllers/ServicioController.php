<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Servicio;
use App\Models\DetalleServicio;
use App\User;
use App\Notifications\NotifyAdmin;
use DB;

class ServicioController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (!$request->ajax()) return redirect('/');

        $servicio = new Servicio();        

        return $servicio->getServicios();  
    }

    public function getServiciosCaja(Request $request)
    {
        if (!$request->ajax()) return redirect('/');

        $servicio = new Servicio();        

        return $servicio->getServiciosCaja();  
    }

    public function getServiciosCajaCerrada(Request $request)
    {
        if (!$request->ajax()) return redirect('/');

        $idcaja = $request->idcaja;

        $servicio = new Servicio();        

        return $servicio->getServiciosCajaCerrada($idcaja);  
    }

    public function obtenerCabecera(Request $request)
    {
        if (!$request->ajax()) return redirect('/');

        $id = $request->id;

        $servicio = new Servicio();        

        return $servicio->obtenerCabecera($id);        
    }

    public function obtenerDetalles(Request $request)
    {
        if (!$request->ajax()) return redirect('/');

        $detalle_servicio = new DetalleServicio();        
        $id = $request->id;

        return $detalle_servicio->obtenerDetalles($id);                
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
            'idcliente' => 'required',
            'tipo_comprobante' => 'required',
            'serie_comprobante' => 'nullable|max:7',
            'num_comprobante' => 'required|max:10',
            'impuesto' => 'required|numeric|between:0, 0.99',      
            'data' => 'array|min:1',      
        ],
        [
            'idcliente.required' => 'El campo cliente es obligatorio.',                        
            'num_comprobante.required' => 'El campo nro. comprobante es obligatorio.',                        
            'serie_comprobante.max' => 'El campo serie comprobante no debe ser mayor a 7 digítos.',                        
            'num_comprobante.max' => 'El campo nro. comprobante no debe ser mayor a 10 digítos.',                        
            'impuesto.numeric' => 'El campo impuesto debe ser un número decimal.',
            'data.min' => 'Seleccione por lo menos un artículo para detalles del servicio.',            
        ]);

        try {
            DB::beginTransaction();

            $mytime= Carbon::now('America/Lima');

            $servicio = new Servicio();
            
            $servicio->idcliente = $request->idcliente;
            $servicio->idusuario = \Auth::user()->id;
            $servicio->tipo_comprobante = $request->tipo_comprobante;
            $servicio->serie_comprobante = $request->serie_comprobante;
            $servicio->num_comprobante = $request->num_comprobante;
            $servicio->fecha_hora = $mytime->format('Y-m-d H:i:s');
            $servicio->impuesto = $request->impuesto;
            $servicio->total = $request->total;
            $servicio->estado = 'Por recoger';
            
            $servicio->save();

            $detalles = $request->data;//Array de detalles
            //Recorro todos los elementos

            foreach($detalles as $ep=>$det)
            {
                $detalle = new DetalleServicio();

                $detalle->idservicio = $servicio->id;
                $detalle->descripcion = $det['descripcion'];
                $detalle->cantidad = $det['cantidad'];
                $detalle->precio = $det['precio'];          
                $detalle->descuento = $det['descuento'];          
                
                $detalle->save();
            }

            /*$fechaActual = date('Y-m-d');

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
            }*/
            
            DB::commit();
            return [
                'id' => $servicio->id
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
    public function actualizar_servicio_caja(Request $request, $id)
    {
        if (!$request->ajax()) return redirect('/');      
       
        
        $servicio = Servicio::findOrFail($id);

        $servicio->idcaja = $request->idcaja;     
        $servicio->estado = "Registrado";        
        
        $servicio->save();        
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
        
        $servicio = Servicio::findOrFail($id);
        $servicio->estado = 'Anulado';

        $servicio->save();
    }

    public function pdf(Request $request, $id){
        $servicio = Servicio::join('personas','servicios.idcliente','=','personas.id')
            ->join('users','servicios.idusuario','=','users.id')
            ->select('servicios.id','servicios.tipo_comprobante','servicios.serie_comprobante',
            'servicios.num_comprobante','servicios.fecha_hora','servicios.impuesto','servicios.total',
            'servicios.estado','personas.nombre','personas.tipo_documento','personas.num_documento',
            'personas.direccion','personas.email',
            'personas.telefono','users.usuario')
            ->where('servicios.id','=',$id)
            ->orderBy('servicios.id', 'desc')->take(1)->get();

        $detalles = DetalleServicio::select('detalle_servicios.cantidad','detalle_servicios.precio','detalle_servicios.descuento',
            'detalle_servicios.descripcion')
            ->where('detalle_servicios.idservicio','=',$id)
            ->orderBy('detalle_servicios.id', 'desc')->get();

        $numservicio=Servicio::select('num_comprobante')->where('id',$id)->get();

        $pdf = \PDF::loadView('pdf.servicio',['servicio'=>$servicio,'detalles'=>$detalles]);        
        
        return $pdf->stream('servicio-'.$numservicio[0]->num_comprobante.'.pdf', array("Attachment" => false));    
    }

    public function pdfTicket(Request $request,$id){
        $servicio = Servicio::join('personas','servicios.idcliente','=','personas.id')
            ->join('users','servicios.idusuario','=','users.id')
            ->select('servicios.id','servicios.tipo_comprobante','servicios.serie_comprobante',
            'servicios.num_comprobante','servicios.fecha_hora','servicios.impuesto','servicios.total',
            'servicios.estado','personas.nombre','personas.tipo_documento','personas.num_documento',
            'personas.direccion','personas.email',
            'personas.telefono','users.usuario')
            ->where('servicios.id','=',$id)
            ->orderBy('servicios.id', 'desc')->take(1)->get();

        $detalles = DetalleServicio::select('detalle_servicios.cantidad','detalle_servicios.precio','detalle_servicios.descuento',
            'detalle_servicios.descripcion')
            ->where('detalle_servicios.idservicio','=',$id)
            ->orderBy('detalle_servicios.id', 'desc')->get();

        $numservicio=Servicio::select('num_comprobante')->where('id',$id)->get();

        $pdf = \PDF::loadView('pdf.servicioticket',['servicio'=>$servicio,'detalles'=>$detalles]);
        
        return $pdf->stream('servicioTicket-'.$numservicio[0]->num_comprobante.'.pdf', array("Attachment" => false));    
    }

    public function listarPdf()
    {
        $servicios = Servicio::join('personas','servicios.idcliente','=','personas.id')
            ->join('users','servicios.idusuario','=','users.id')
            ->select('servicios.id','servicios.tipo_comprobante','servicios.serie_comprobante',
            'servicios.num_comprobante','servicios.fecha_hora','servicios.impuesto','servicios.total',
            'servicios.estado','personas.nombre','users.usuario')
            ->orderBy('servicios.id', 'desc')->take(100)->get();
        
        $cont = Servicio::count();

        if ($cont < 100) {
            $cont = Servicio::count();
        }
        else {
            $cont = 100;
        }

        $pdf = \PDF::loadView('pdf.serviciospdf',['servicios'=>$servicios,'cont'=>$cont])->setPaper('a4', 'landscape');

        return $pdf->stream('servicios.pdf', array("Attachment" => false));            
    }
}
