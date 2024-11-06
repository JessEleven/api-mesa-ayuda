<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
        'id_prioridad',
        'id_calificacion',
    ];

    public function tecnico_asignados()
    {
        return $this->hasMany(TecnicoAsignado::class, 'id_tecnico_asignado');
    }

    public function categorias()
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
        return $this->belongsTo(CalificacionTicket::class, 'id_calificacion');
    }
}
