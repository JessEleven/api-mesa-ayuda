<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    protected $table = 'areas';

    protected $fillable = [
        'nombre_area',
        'peso_prioridad',
    ];

    public function usuarios()
    {
        return $this->hasMany(Usuario::class, 'id_area');
    }

}
