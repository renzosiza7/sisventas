<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class Marca extends Model
{
    protected $table = 'marcas';

    protected $fillable = ['nombre', 'descripcion', 'acceso', 'condicion'];

    public function articulos()
    {
        return $this->hasMany('App\Models\Articulo');
    }

    public function getMarcasActivas() {

        $marcas = DB::table($this->table)                                                                                                                    
                        ->select('id', 'nombre')
                        ->where('condicion', '=', '1')
                        ->orderBy('nombre', 'asc')
                        ->get();

        return $marcas;  
    }

    public function listarPdf() {

        $marcas = DB::table($this->table)                                                                                                                    
                        ->select('id', 'nombre', 'descripcion', 'condicion')                        
                        ->orderBy('nombre', 'asc')
                        ->get();

        return json_encode($marcas);  
    }
}
