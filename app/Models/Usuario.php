<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Usuario extends Model
{
    protected $table = 'usuarios';

    protected $fillable = [
        'nombre',
        'apellido',
        'telefono',
        'email',
        'password',
        'id_departamento'
    ];

    protected $hidden = [
        'password'
    ];

    public function usuario_roles()
    {
        return $this->hasOne(UsuarioRol::class, 'id_usuario');
    }

    // Queda revision para despejar la duda...
    public function tecnico_asignado()
    {
        return $this->hasMany(TecnicoAsignado::class, 'id_usuario');
    }

    public function departamentos()
    {
        return $this->belongsTo(Departamento::class, 'id_departamento');
    }

    // Accesor para created_at y updated_at
    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->timezone(config('app.timezone'))->format('d/m/Y H:i:s');
    }

    public function getUpdatedAtAttribute($value)
    {
        return Carbon::parse($value)->timezone(config('app.timezone'))->format('d/m/Y H:i:s');
    }
}
