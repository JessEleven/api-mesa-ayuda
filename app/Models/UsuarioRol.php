<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsuarioRol extends Model
{
    protected $table = 'usuarios_roles';

    protected $fillable = [
        'id_rol',
        'id_usuario'
    ];

    public function roles()
    {
        return $this->belongsTo(Rol::class, 'id_rol');
    }

    public function usuarios()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }
}
