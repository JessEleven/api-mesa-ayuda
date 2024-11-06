<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    protected $table = 'roles';

    protected $fillable = [
        'descripcion',
        'estado_eliminado',
        'fecha_registro'
    ];

    public function roles_permisos()
    {
        return $this->hasMany(RolPermiso::class, 'id_rol');
    }

    public function usuario_roles()
    {
        return $this->hasMany(UsuarioRol::class, 'id_rol');
    }

}
