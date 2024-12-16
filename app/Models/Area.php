<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Area extends Model
{
    protected $table = 'areas';

    protected $fillable = [
        'nombre_area',
        'sigla_area',
        'secuencia_area',
        'peso_prioridad',
    ];

    public function departamentos()
    {
        return $this->hasMany(Departamento::class, 'id_area');
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
