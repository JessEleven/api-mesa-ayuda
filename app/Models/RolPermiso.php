<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RolPermiso extends Model
{
    protected $table = 'roles_permisos';

    protected $fillable = [
        'fecha_registro',
        'id_rol',
        'id_permiso'
    ];

    public function roles()
    {
        return $this->belongsTo(Rol::class, 'id_rol');
    }
    public function permisos()
    {
        return $this->belongsTo(Permiso::class, 'id_permiso');
    }
}
