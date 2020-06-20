<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class DetalleServicio extends Model
{
    protected $table = 'detalle_servicios';
    protected $fillable = [
        'idservicio', 
        'descripcion',
        'cantidad',
        'precio',
        'descuento'
    ];
    public $timestamps = false;

    public function obtenerDetalles($id) {

        $detalles = DB::table($this->table)                                                      
                        ->select('detalle_servicios.cantidad','detalle_servicios.precio',
                                 'detalle_servicios.descuento','detalle_servicios.descripcion')
                        ->where('detalle_servicios.idservicio','=',$id)
                        ->orderBy('detalle_servicios.id', 'desc')
                        ->get();
        
        return $detalles;
    }
}
