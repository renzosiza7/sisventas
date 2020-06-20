<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class Articulo extends Model
{
    protected $table = 'articulos';
    protected $fillable =[
        'idcategoria',
        'idmarca',
        'codigo',
        'nombre',
        'precio_venta',
        'stock',
        'descripcion',
        'condicion'
    ];

    public function categoria(){
        return $this->belongsTo('App\Models\Categoria');
    }

    public function marca(){
        return $this->belongsTo('App\Models\Marca');
    }

    public function getArticulos() {

        $articulos = DB::table($this->table)         
                        ->join('categorias', 'articulos.idcategoria', '=', 'categorias.id')
                        ->join('marcas', 'articulos.idmarca', '=', 'marcas.id')                                                                                      
                        ->select('articulos.id', 'articulos.codigo', 'articulos.nombre',
                                 'categorias.id as idcategoria', 'marcas.id as idmarca',
                                 'categorias.nombre as categoria', 'marcas.nombre as marca',
                                 'articulos.precio_compra', 'articulos.precio_venta', 'articulos.stock',
                                 'articulos.descripcion', 'articulos.condicion')
                        ->orderBy('articulos.id', 'desc')
                        ->get();

        return $articulos;  
    }

    public function getArticulosPdf() {

        $articulos = DB::table($this->table)         
                        ->join('categorias', 'articulos.idcategoria', '=', 'categorias.id')
                        ->join('marcas', 'articulos.idmarca', '=', 'marcas.id')                                                                                      
                        ->select('articulos.id', 'articulos.codigo', 'articulos.nombre',
                                 'categorias.id as idcategoria', 'marcas.id as idmarca',
                                 'categorias.nombre as categoria', 'marcas.nombre as marca',
                                 'articulos.precio_venta', 'articulos.stock',
                                 'articulos.descripcion', 'articulos.condicion')                        
                        ->orderBy('articulos.nombre', 'asc')
                        ->limit(100)
                        ->get();

        return json_encode($articulos);  
    }

    public function buscarArticulo($filtro) {     

        $articulo = DB::table($this->table)                                 
                        ->where('codigo','=', $filtro)
                        ->select('id','nombre')
                        ->orderBy('nombre', 'asc')                        
                        ->first();

        return json_encode($articulo);        
    }

    public function buscarArticuloVenta($filtro) {     

        $articulo = DB::table($this->table)                                 
                        ->where('codigo','=', $filtro)
                        ->select('id','nombre', 'stock', 'precio_venta')
                        ->where('stock','>', '0')
                        ->orderBy('nombre', 'asc')                        
                        ->first();

        return json_encode($articulo);        
    }

    public function listarArticulo($buscar, $criterio)
    {
        if ($buscar == '') {
            $articulos = DB::table($this->table)         
                        ->join('categorias', 'articulos.idcategoria', '=', 'categorias.id')
                        ->join('marcas', 'articulos.idmarca', '=', 'marcas.id')                                                                                      
                        ->select('articulos.id', 'articulos.codigo', 'articulos.nombre',
                                 'categorias.id as idcategoria', 'marcas.id as idmarca',
                                 'categorias.nombre as categoria', 'marcas.nombre as marca',
                                 'articulos.precio_venta', 'articulos.stock',
                                 'articulos.descripcion', 'articulos.condicion')
                        ->where('articulos.condicion','=', '1')
                        ->orderBy('articulos.id', 'desc')
                        ->limit(5)
                        ->get();
        }
        else {
            $articulos = DB::table($this->table)         
                        ->join('categorias', 'articulos.idcategoria', '=', 'categorias.id')
                        ->join('marcas', 'articulos.idmarca', '=', 'marcas.id')                                                                                      
                        ->select('articulos.id', 'articulos.codigo', 'articulos.nombre',
                                 'categorias.id as idcategoria', 'marcas.id as idmarca',
                                 'categorias.nombre as categoria', 'marcas.nombre as marca',
                                 'articulos.precio_venta', 'articulos.stock',
                                 'articulos.descripcion', 'articulos.condicion')
                        ->where('articulos.'.$criterio, 'like', '%'. $buscar . '%')
                        ->where('articulos.condicion','=', '1')
                        ->orderBy('articulos.id', 'desc')
                        ->limit(5)
                        ->get();
        }

        return $articulos;
    }

    public function listarArticuloVenta($buscar, $criterio)
    {
        if ($buscar == '') {
            $articulos = DB::table($this->table)         
                        ->join('categorias', 'articulos.idcategoria', '=', 'categorias.id')
                        ->join('marcas', 'articulos.idmarca', '=', 'marcas.id')                                                                                      
                        ->select('articulos.id', 'articulos.codigo', 'articulos.nombre',
                                 'categorias.id as idcategoria', 'marcas.id as idmarca',
                                 'categorias.nombre as categoria', 'marcas.nombre as marca',
                                 'articulos.precio_venta', 'articulos.stock',
                                 'articulos.descripcion', 'articulos.condicion')
                        ->where('articulos.stock', '>', '0')
                        ->where('articulos.condicion','=', '1')
                        ->orderBy('articulos.id', 'desc')
                        ->limit(5)
                        ->get();
        }
        else {
            $articulos = DB::table($this->table)         
                        ->join('categorias', 'articulos.idcategoria', '=', 'categorias.id')
                        ->join('marcas', 'articulos.idmarca', '=', 'marcas.id')                                                                                      
                        ->select('articulos.id', 'articulos.codigo', 'articulos.nombre',
                                 'categorias.id as idcategoria', 'marcas.id as idmarca',
                                 'categorias.nombre as categoria', 'marcas.nombre as marca',
                                 'articulos.precio_venta', 'articulos.stock',
                                 'articulos.descripcion', 'articulos.condicion')
                        ->where('articulos.'.$criterio, 'like', '%'. $buscar . '%')
                        ->where('articulos.stock', '>', '0')
                        ->where('articulos.condicion','=', '1')
                        ->orderBy('articulos.id', 'desc')
                        ->limit(5)
                        ->get();
        }

        return $articulos;
    }
}
