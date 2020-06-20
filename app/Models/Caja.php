<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Caja extends Model
{
    protected $table = 'cajas';
    protected $fillable =[
        'total_ventas',        
        'total_servicios',
        'total_mov_entrada',
        'total_mov_salida',
        'total_gastos',
        'saldo_sistema',
        'saldo_caja',
        'diferencia',
        'estado',
        'idcajero'        
    ];   
}
