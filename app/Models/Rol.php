<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class Rol extends Model
{
    protected $table = 'roles';
    protected $fillable = ['nombre','descripcion','condicion'];
    public $timestamps = false;

    public function users()
    {
        return $this->hasMany('App\User');
    }

    public function getRolesActivos() {

        $roles = DB::table($this->table)                                                                                                                    
                        ->select('id', 'nombre')
                        ->where('condicion', '=', '1')
                        ->orderBy('nombre', 'asc')
                        ->get();

        return $roles;  
    }
}
