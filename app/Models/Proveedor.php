<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class Proveedor extends Model
{
    protected $table = 'proveedores';
    protected $fillable = [
        'id', 'contacto', 'telefono_contacto'
    ];

    public $timestamps = false;

    public function persona()
    {
        return $this->belongsTo('App\Models\Persona');
    }

    public function getProveedores() {

        $proveedores = DB::table($this->table)         
                        ->join('personas', 'proveedores.id', '=', 'personas.id')
                        ->select('personas.id','personas.nombre','personas.tipo_documento',
                            'personas.num_documento','personas.direccion','personas.telefono',
                            'personas.email','proveedores.contacto','proveedores.telefono_contacto')
                        ->orderBy('personas.id', 'desc')
                        ->get();

        return $proveedores;  
    }

    public function selectProveedor($filtro) {

        $proveedores = DB::table($this->table)         
                        ->join('personas', 'proveedores.id', '=', 'personas.id')
                        ->where('personas.nombre', 'like', '%'. $filtro . '%')
                        ->orWhere('personas.num_documento', 'like', '%'. $filtro . '%')
                        ->select('personas.id','personas.nombre','personas.num_documento')
                        ->orderBy('personas.nombre', 'asc')
                        ->get();

        return $proveedores;  
    }

}
