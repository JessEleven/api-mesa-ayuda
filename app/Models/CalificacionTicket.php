<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class CalificacionTicket extends Model
{
    protected $table = 'calificaciones_tickets';

    protected $fillable = [
        'calificacion',
        'observacion',
        'id_ticket'
    ];

    public function tickets()
    {
        return $this->belongsTo(Ticket::class, 'id_ticket');
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
