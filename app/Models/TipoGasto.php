<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class TipoGasto extends Model
{
    protected $table = 'tipo_gastos';

    protected $fillable = ['nombre', 'descripcion', 'condicion'];

    /*public function articulos()
    {
        return $this->hasMany('App\Models\Articulo');
    }*/   

    public function getTipoGastoActivos() {

        $tipo_gastos = DB::table($this->table)                                                                                                                    
                        ->select('id', 'nombre')
                        ->where('condicion', '=', '1')
                        ->orderBy('nombre', 'asc')
                        ->get();

        return $tipo_gastos;  
    }    
}
