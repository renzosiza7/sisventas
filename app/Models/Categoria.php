<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class Categoria extends Model
{
    protected $table = 'categorias';

    protected $fillable = ['nombre', 'descripcion', 'acceso', 'imagen', 'condicion'];

    public function articulos()
    {
        return $this->hasMany('App\Models\Articulo');
    }   

    public function getCategoriasActivas() {

        $categorias = DB::table($this->table)                                                                                                                    
                        ->select('id', 'nombre')
                        ->where('condicion', '=', '1')
                        ->orderBy('nombre', 'asc')
                        ->get();

        return $categorias;  
    }

    public function listarPdf() {

        $categorias = DB::table($this->table)                                                                                                                    
                        ->select('id', 'nombre', 'descripcion', 'condicion')                        
                        ->orderBy('nombre', 'asc')
                        ->get();

        return json_encode($categorias);  
    }
}
