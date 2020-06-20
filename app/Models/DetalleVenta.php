<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class DetalleVenta extends Model
{
    protected $table = 'detalle_ventas';
    protected $fillable = [
        'idventa', 
        'idarticulo',
        'cantidad',
        'precio',
        'descuento'
    ];
    public $timestamps = false;

    public function obtenerDetalles($id) {

        $detalles = DB::table($this->table)         
                        ->join('articulos','detalle_ventas.idarticulo','=','articulos.id')                        
                        ->select('detalle_ventas.cantidad','detalle_ventas.precio',
                                 'detalle_ventas.descuento','articulos.nombre as articulo')
                        ->where('detalle_ventas.idventa','=',$id)
                        ->orderBy('detalle_ventas.id', 'desc')
                        ->get();
        
        return $detalles;
    }
}
