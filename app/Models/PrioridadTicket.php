<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrioridadTicket extends Model
{
    protected $table = 'prioridad_tickets';

    protected $fillable = [
        'nombre_prioridad',
        'color_prioridad',
        'orden_prioridad'
    ];

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'id_prioridad');
    }
}
