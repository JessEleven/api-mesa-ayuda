<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Ticket extends Model
{
    protected $table = 'tickets';

    protected $fillable = [
        'codigo_ticket',
        'descripcion',
        'fecha_inicio',
        'fecha_fin',
        'id_categoria',
        'id_usuario',
        'id_estado',
        'id_prioridad'
    ];

    protected static function boot()
    {
        parent::boot();

        static::updating(function ($ticket) {
            // Se verifica si el estado ha cambiado
            if ($ticket->isDirty('id_estado')) {
                $nuevoEstado = EstadoTicket::find($ticket->id_estado);

                // Se obtiene el estado con la mayor orden de prioridad
                $mayorPrioridad = EstadoTicket::max('orden_prioridad');

                // Si el nuevo estado tiene la orden de prioridad más alta, se asigna la fecha fin
                if ($nuevoEstado && $nuevoEstado->orden_prioridad === $mayorPrioridad) {
                    $ticket->fecha_fin = Carbon::now();
                }
            }
        });
    }

    public function tecnico_asignados()
    {
        return $this->hasOne(TecnicoAsignado::class, 'id_tecnico_asignado');
    }

    public function categoria_tickets()
    {
        return $this->belongsTo(CategoriaTicket::class, 'id_categoria');
    }

    public function usuarios()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }

    public function estado_tickets()
    {
        return $this->belongsTo(EstadoTicket::class, 'id_estado');
    }

    public function prioridad_tickets()
    {
        return $this->belongsTo(PrioridadTicket::class, 'id_prioridad');
    }

    public function calificacion_tickets()
    {
        return $this->hasOne(CalificacionTicket::class, 'id_calificacion');
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
