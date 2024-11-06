<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstadoTicket extends Model
{
    protected $table = 'estado_tickets';

    protected $fillable = [
        'nombre_estado',
        'color_estado',
        'orden_prioridad',
    ];

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'id_estado');
    }
}
