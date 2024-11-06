<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permiso extends Model
{
    protected $table = 'permisos';

    protected $fillable = [
        'descripcion',
        'estado_eliminado',
        'fecha_registro'
    ];

    public function roles_permisos()
    {
        return $this->hasMany(RolPermiso::class, 'id_permiso');
    }
}

