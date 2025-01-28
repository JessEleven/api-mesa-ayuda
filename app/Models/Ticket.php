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

    // Para marcar un registro como eliminado
    public function delete()
    {
        $this->recurso_eliminado = now();
        $this->save();
    }

    // Por si se quiere restaurar un registro eliminado
    public function restore()
    {
        $this->recurso_eliminado = null;
        $this->save();
    }

    // Para verificar si el registro está eliminado
    public function trashed()
    {
        return !is_null($this->recurso_eliminado);
    }

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

    // Accesor para registro_eliminado
    public function getRecursoEliminadoAttribute($value)
    {
        return $value ? Carbon::parse($value)->timezone(config('app.timezone'))->format('d/m/Y H:i:s') : null;
    }

    // Accessor para fecha_inicio y fecha_fin
    public function getFechaInicioAttribute($value)
    {
        return Carbon::parse($value)->timezone(config('app.timezone'))->format('d/m/Y H:i:s');
    }

    public function getFechaFinAttribute($value)
    {
        return $value ? Carbon::parse($value)->timezone(config('app.timezone'))->format('d/m/Y H:i:s') : null;
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
