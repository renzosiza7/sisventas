<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class Servicio extends Model
{
    protected $table = 'servicios';

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

    public function getServicios() {

        $servicios = DB::table($this->table)         
                        ->join('personas', 'servicios.idcliente', '=', 'personas.id')
                        ->join('users', 'servicios.idusuario', '=', 'users.id')                                                                                      
                        ->select('servicios.id','servicios.idusuario','servicios.tipo_comprobante','servicios.serie_comprobante',
                                 'servicios.num_comprobante','servicios.fecha_hora','servicios.impuesto','servicios.total',
                                 'servicios.estado','personas.nombre','users.usuario')
                        ->orderBy('servicios.id', 'desc')
                        ->get();                        

        return $servicios;  
    }

    public function getServiciosCaja() {

        $servicios = DB::table($this->table)         
                        ->join('personas', 'servicios.idcliente', '=', 'personas.id')
                        ->join('users', 'servicios.idusuario', '=', 'users.id')                                                                                      
                        ->where('servicios.estado', '=', 'Por recoger')
                        ->select('servicios.id','servicios.tipo_comprobante','servicios.serie_comprobante',
                                 'servicios.num_comprobante','servicios.fecha_hora','servicios.impuesto','servicios.total',
                                 'servicios.estado','personas.nombre','users.usuario')
                        ->orderBy('servicios.id', 'desc')
                        ->get();                        

        return $servicios;  
    }

    public function getServiciosCajaCerrada($idcaja) {

        $servicios = DB::table($this->table)         
                        ->join('personas', 'servicios.idcliente', '=', 'personas.id')
                        ->join('users', 'servicios.idusuario', '=', 'users.id')                                                                                      
                        ->where('servicios.estado', '=', 'Registrado')
                        ->where('servicios.idcaja', '=', $idcaja)
                        ->select('servicios.id','servicios.tipo_comprobante','servicios.serie_comprobante',
                                 'servicios.num_comprobante','servicios.fecha_hora','servicios.impuesto','servicios.total',
                                 'servicios.estado','personas.nombre','users.usuario')
                        ->orderBy('servicios.id', 'desc')
                        ->get();                        

        return $servicios;  
    }

    public function obtenerCabecera($id) {

        $venta = DB::table($this->table)         
                        ->join('personas', 'servicios.idcliente', '=', 'personas.id')                        
                        ->join('users', 'servicios.idusuario', '=', 'users.id')                                                                                      
                        ->select('servicios.id','servicios.tipo_comprobante','servicios.serie_comprobante',
                                 'servicios.num_comprobante','servicios.fecha_hora','servicios.impuesto','servicios.total',
                                 'servicios.estado','personas.nombre','users.usuario')
                        ->where('servicios.id','=',$id)
                        ->orderBy('servicios.id', 'desc')
                        ->first();
        
        return json_encode($venta);
    }  
}
