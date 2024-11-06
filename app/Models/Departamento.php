<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Departamento extends Model
{
    protected $table = 'departamentos';

    protected $fillable = [
        'nombre_departamento',
        'peso_prioridad',
    ];

    public function usuarios()
    {
        return $this->hasMany(Usuario::class, 'id_departamento');
    }

}
