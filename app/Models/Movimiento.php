<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class Movimiento extends Model
{
    protected $table = 'movimientos';
    protected $fillable =[
        'monto',
        'tipo',
        'descripcion',
        'idcaja'        
    ];       
}
