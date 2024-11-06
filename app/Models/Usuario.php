<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    protected $table = 'usuarios';

    protected $fillable = [
        'nombre',
        'apellido',
        'telefono',
        'email',
        'password',
        'id_area',
        'id_departamento'
    ];

    protected $hidden = [
        'password'
    ];

    public function usuario_roles()
    {
        return $this->hasMany(UsuarioRol::class, 'id_usuario');
    }

    // Queda revision para despejar la duda...
    // public function tecnico_asignado()
    // {
    //     return $this->hasMany(TecnicoAsignado::class, 'id_usuario');
    // }

    public function areas()
    {
        return $this->belongsTo(Area::class, 'id_area');
    }

    public function departamentos()
    {
        return $this->belongsTo(Departamento::class, 'id_departamento');
    }
}
