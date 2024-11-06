<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CalificacionTicket extends Model
{
    protected $table = 'calificacion_tickets';

    protected $fillable = [
        'calificacion',
        'observacion',
    ];

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'id_calificacion');
    }
}
