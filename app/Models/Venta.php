<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class Venta extends Model
{
    protected $table = 'ventas';

    protected $fillable =[
        'idcliente', 
        'idusuario',
        'tipo_comprobante',
        'serie_comprobante',
        'num_comprobante',
        'fecha_hora',
        'impuesto',
        'total',
        'estado'
    ];

    public function getVentas() {

        $ventas = DB::table($this->table)         
                        ->join('personas', 'ventas.idcliente', '=', 'personas.id')
                        ->join('users', 'ventas.idusuario', '=', 'users.id')                                                                                      
                        ->select('ventas.id', 'ventas.idusuario','ventas.tipo_comprobante','ventas.serie_comprobante',
                                 'ventas.num_comprobante','ventas.fecha_hora','ventas.impuesto','ventas.total',
                                 'ventas.estado','personas.nombre','users.usuario')
                        ->orderBy('ventas.id', 'desc')
                        ->get();                        

        return $ventas;  
    }

    public function getSerie($tipo_comprobante) {

        $serie = DB::table('serie')                                                                                                                 
                        ->select('serie','correlativo')
                        ->where('tipocomp', $tipo_comprobante)                        
                        ->first();                        

        return json_encode($serie);  
    }

    public function getVentasCaja() {

        $ventas = DB::table($this->table)         
                        ->join('personas', 'ventas.idcliente', '=', 'personas.id')
                        ->join('users', 'ventas.idusuario', '=', 'users.id')                                                                                      
                        ->where('ventas.estado', '=', 'Emitido')
                        ->select('ventas.id','ventas.tipo_comprobante','ventas.serie_comprobante',
                                 'ventas.num_comprobante','ventas.fecha_hora','ventas.impuesto','ventas.total',
                                 'ventas.estado','personas.nombre','users.usuario')
                        ->orderBy('ventas.id', 'desc')
                        ->get();                        

        return $ventas;  
    }

    public function getVentasCajaCerrada($idcaja) {

        $ventas = DB::table($this->table)         
                        ->join('personas', 'ventas.idcliente', '=', 'personas.id')
                        ->join('users', 'ventas.idusuario', '=', 'users.id')                                                                                      
                        ->where('ventas.estado', '=', 'Registrado')
                        ->where('ventas.idcaja', '=', $idcaja)
                        ->select('ventas.id','ventas.tipo_comprobante','ventas.serie_comprobante',
                                 'ventas.num_comprobante','ventas.fecha_hora','ventas.impuesto','ventas.total',
                                 'ventas.estado','personas.nombre','users.usuario')
                        ->orderBy('ventas.id', 'desc')
                        ->get();                        

        return $ventas;  
    }

    public function obtenerCabecera($id) {

        $venta = DB::table($this->table)         
                        ->join('personas', 'ventas.idcliente', '=', 'personas.id')                        
                        ->join('users', 'ventas.idusuario', '=', 'users.id')                                                                                      
                        ->select('ventas.id','ventas.tipo_comprobante','ventas.serie_comprobante',
                                'ventas.num_comprobante','ventas.fecha_hora','ventas.impuesto','ventas.total',
                                'ventas.estado','personas.nombre','users.usuario')
                        ->where('ventas.id','=',$id)
                        ->orderBy('ventas.id', 'desc')
                        ->first();
        
        return json_encode($venta);
    }  
}
