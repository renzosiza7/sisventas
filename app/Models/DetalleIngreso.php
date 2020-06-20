<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class DetalleIngreso extends Model
{
    protected $table = 'detalle_ingresos';
    protected $fillable = [
        'idingreso', 
        'idarticulo',
        'cantidad',
        'precio'
    ];
    public $timestamps = false;

    public function obtenerDetalles($id) {

        $detalles = DB::table($this->table)         
                        ->join('articulos','detalle_ingresos.idarticulo','=','articulos.id')                        
                        ->select('detalle_ingresos.cantidad','detalle_ingresos.precio','articulos.nombre as articulo')
                        ->where('detalle_ingresos.idingreso','=',$id)
                        ->orderBy('detalle_ingresos.id', 'desc')
                        ->get();
        
        return $detalles;
    }
}
