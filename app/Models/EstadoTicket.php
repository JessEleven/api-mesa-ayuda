<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class EstadoTicket extends Model
{
    protected $table = 'estados_tickets';

    protected $fillable = [
        'nombre_estado',
        'color_estado',
        'orden_prioridad',
    ];

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'id_estado');
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
