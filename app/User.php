<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use DB;

class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'usuario', 'password','condicion','idrol'
    ];
    
    public $timestamps = false;

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function rol(){
        return $this->belongsTo('App\Models\Rol');
    }

    public function persona(){
        return $this->belongsTo('App\Models\Persona');
    }


    public function getUsuarios() {

        $usuarios = DB::table($this->table)         
                        ->join('personas','users.id','=','personas.id')
                        ->join('roles','users.idrol','=','roles.id')
                        ->select('personas.id','personas.nombre','personas.tipo_documento',
                                 'personas.num_documento','personas.direccion','personas.telefono',
                                 'personas.email','users.usuario','users.password',
                                 'users.condicion','users.idrol','roles.nombre as rol')
                        ->orderBy('users.id', 'desc')
                        ->get();

        return $usuarios;  
    }
}
