<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class Ingreso extends Model
{
    protected $table = 'ingresos';
    
    protected $fillable = [
        'idproveedor', 
        'idusuario',
        'tipo_comprobante',
        'serie_comprobante',
        'num_comprobante',
        'fecha_hora',
        'impuesto',
        'total',
        'estado'
     ];

     public function usuario()
     {
         return $this->belongsTo('App\User');
     }
     
     public function proveedor()
     {
         return $this->belongsTo('App\Models\Proveedor');
     }

     public function getIngresos() {

        $ingresos = DB::table($this->table)         
                        ->join('personas', 'ingresos.idproveedor', '=', 'personas.id')
                        ->join('users', 'ingresos.idusuario', '=', 'users.id')                                                                                      
                        ->select('ingresos.id', 'ingresos.idusuario', 'ingresos.tipo_comprobante', 'ingresos.serie_comprobante',
                                 'ingresos.num_comprobante', 'ingresos.fecha_hora', 'ingresos.impuesto',
                                 'ingresos.total', 'ingresos.estado', 'personas.nombre as proveedor','users.usuario')
                        ->orderBy('ingresos.id', 'desc')
                        ->get();

        return $ingresos;  
    }

    public function obtenerCabecera($id) {

        $ingreso = DB::table($this->table)         
                        ->join('personas', 'ingresos.idproveedor', '=', 'personas.id')                        
                        ->join('users', 'ingresos.idusuario', '=', 'users.id')                                                                                      
                        ->select('ingresos.id','ingresos.tipo_comprobante','ingresos.serie_comprobante',
                                'ingresos.num_comprobante','ingresos.fecha_hora','ingresos.impuesto','ingresos.total',
                                'ingresos.estado','personas.nombre','users.usuario')
                        ->where('ingresos.id','=',$id)
                        ->orderBy('ingresos.id', 'desc')
                        ->first();
        
        return json_encode($ingreso);
    }    
}
