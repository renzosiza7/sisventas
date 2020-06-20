<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Venta;
use App\Models\DetalleVenta;
use App\User;
use App\Notifications\NotifyAdmin;
use DB;

class VentaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (!$request->ajax()) return redirect('/');

        $venta = new Venta();        

        return $venta->getVentas();  
    }

    public function getVentasCaja(Request $request)
    {
        if (!$request->ajax()) return redirect('/');

        $venta = new Venta();        

        return $venta->getVentasCaja();  
    }

    public function getVentasCajaCerrada(Request $request)
    {
        if (!$request->ajax()) return redirect('/');

        $idcaja = $request->idcaja;

        $venta = new Venta();        

        return $venta->getVentasCajaCerrada($idcaja);  
    }

    public function obtenerCabecera(Request $request)
    {
        if (!$request->ajax()) return redirect('/');

        $id = $request->id;

        $venta = new Venta();        

        return $venta->obtenerCabecera($id);        
    }

    public function obtenerDetalles(Request $request)
    {
        if (!$request->ajax()) return redirect('/');

        $detalle_venta = new DetalleVenta();        
        $id = $request->id;

        return $detalle_venta->obtenerDetalles($id);                
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
            'data.min' => 'Seleccione por lo menos un artículo para detalles de la venta.',            
        ]);

        try {
            DB::beginTransaction();

            $mytime= Carbon::now('America/Lima');

            $venta = new Venta();
            
            $venta->idcliente = $request->idcliente;
            $venta->idusuario = \Auth::user()->id;
            $venta->tipo_comprobante = $request->tipo_comprobante;
            $venta->serie_comprobante = $request->serie_comprobante;
            $venta->num_comprobante = $request->num_comprobante;
            $venta->fecha_hora = $mytime->format('Y-m-d H:i:s');
            $venta->impuesto = $request->impuesto;
            $venta->total = $request->total;
            $venta->estado = 'Emitido';
            
            $venta->save();

            $detalles = $request->data;//Array de detalles
            //Recorro todos los elementos

            foreach($detalles as $ep=>$det)
            {
                $detalle = new DetalleVenta();

                $detalle->idventa = $venta->id;
                $detalle->idarticulo = $det['idarticulo'];
                $detalle->cantidad = $det['cantidad'];
                $detalle->precio = $det['precio'];          
                $detalle->descuento = $det['descuento'];          
                
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
                'id' => $venta->id
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
    public function actualizar_venta_caja(Request $request, $id)
    {
        if (!$request->ajax()) return redirect('/');      
       
        
        $venta = Venta::findOrFail($id);

        $venta->idcaja = $request->idcaja;     
        $venta->estado = "Registrado";        
        
        $venta->save();        
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
        
        $venta = Venta::findOrFail($id);
        $venta->estado = 'Anulado';

        $venta->save();
    }

    public function pdf(Request $request,$id){
        $venta = Venta::join('personas','ventas.idcliente','=','personas.id')
        ->join('users','ventas.idusuario','=','users.id')
        ->select('ventas.id','ventas.tipo_comprobante','ventas.serie_comprobante',
        'ventas.num_comprobante','ventas.fecha_hora','ventas.impuesto','ventas.total',
        'ventas.estado','personas.nombre','personas.tipo_documento','personas.num_documento',
        'personas.direccion','personas.email',
        'personas.telefono','users.usuario')
        ->where('ventas.id','=',$id)
        ->orderBy('ventas.id', 'desc')->take(1)->get();

        $detalles = DetalleVenta::join('articulos','detalle_ventas.idarticulo','=','articulos.id')
        ->select('detalle_ventas.cantidad','detalle_ventas.precio','detalle_ventas.descuento',
        'articulos.nombre as articulo')
        ->where('detalle_ventas.idventa','=',$id)
        ->orderBy('detalle_ventas.id', 'desc')->get();

        $numventa=Venta::select('num_comprobante')->where('id',$id)->get();

        $pdf = \PDF::loadView('pdf.venta',['venta'=>$venta,'detalles'=>$detalles]);
        //return $pdf->download('venta-'.$numventa[0]->num_comprobante.'.pdf');       
        
        return $pdf->stream('venta-'.$numventa[0]->num_comprobante.'.pdf', array("Attachment" => false));    
    }

    public function pdfTicket(Request $request,$id){
        $venta = Venta::join('personas','ventas.idcliente','=','personas.id')
        ->join('users','ventas.idusuario','=','users.id')
        ->select('ventas.id','ventas.tipo_comprobante','ventas.serie_comprobante',
        'ventas.num_comprobante','ventas.fecha_hora','ventas.impuesto','ventas.total',
        'ventas.estado','personas.nombre','personas.tipo_documento','personas.num_documento',
        'personas.direccion','personas.email',
        'personas.telefono','users.usuario')
        ->where('ventas.id','=',$id)
        ->orderBy('ventas.id', 'desc')->take(1)->get();

        $detalles = DetalleVenta::join('articulos','detalle_ventas.idarticulo','=','articulos.id')
        ->select('detalle_ventas.cantidad','detalle_ventas.precio','detalle_ventas.descuento',
        'articulos.nombre as articulo')
        ->where('detalle_ventas.idventa','=',$id)
        ->orderBy('detalle_ventas.id', 'desc')->get();

        $numventa=Venta::select('num_comprobante')->where('id',$id)->get();

        $pdf = \PDF::loadView('pdf.ventaticket',['venta'=>$venta,'detalles'=>$detalles]);
        
        return $pdf->stream('ventaTicket-'.$numventa[0]->num_comprobante.'.pdf', array("Attachment" => false));    
    }

    public function listarPdf()
    {
        $ventas = Venta::join('personas','ventas.idcliente','=','personas.id')
            ->join('users','ventas.idusuario','=','users.id')
            ->select('ventas.id','ventas.tipo_comprobante','ventas.serie_comprobante',
            'ventas.num_comprobante','ventas.fecha_hora','ventas.impuesto','ventas.total',
            'ventas.estado','personas.nombre','users.usuario')
            ->orderBy('ventas.id', 'desc')->take(100)->get();        
        
        $cont=Venta::count();

        if ($cont < 100) {
            $cont=Venta::count();
        }
        else {
            $cont = 100;
        }

        $pdf = \PDF::loadView('pdf.ventaspdf',['ventas'=>$ventas,'cont'=>$cont])->setPaper('a4', 'landscape');

        return $pdf->stream('ventas.pdf', array("Attachment" => false));            
    }
}
