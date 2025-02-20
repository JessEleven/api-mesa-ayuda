<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Departamento extends Model
{
    protected $table = 'departamentos';

    protected $fillable = [
        'nombre_departamento',
        'sigla_departamento',
        'secuencia_departamento',
        'peso_prioridad',
        'id_area'
    ];

    protected $hidden = [
        'id_area'
    ];

    public function usuarios()
    {
        return $this->hasMany(Usuario::class, 'id_departamento');
    }

    public function area()
    {
        return $this->belongsTo(Area::class, 'id_area');
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
