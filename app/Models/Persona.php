<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class Persona extends Model
{
    protected $table = 'personas';
    protected $fillable = ['nombre','tipo_documento','num_documento','direccion','telefono','email'];

    public function proveedor()
    {
        return $this->hasOne('App\Models\Proveedor');
    }

    public function user()
    {
        return $this->hasOne('App\User');
    }

    public function selectCliente($filtro)
    {
        $clientes = DB::table($this->table)                                 
                        ->where('nombre', 'like', '%'. $filtro . '%')
                        ->orWhere('num_documento', 'like', '%'. $filtro . '%')
                        ->select('id','nombre','num_documento')
                        ->orderBy('nombre', 'asc')
                        ->get();

        return $clientes; 
    }
}
