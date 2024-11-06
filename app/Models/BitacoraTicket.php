<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BitacoraTicket extends Model
{
    protected $table = 'bitacora_tickets';

    protected $fillable = [
        'descripcion',
        'estado_eliminado',
        'fecha_registro',
        'id_tecnico_asignado'
    ];

    public function tecnico_asigados()
    {
        return $this->belongsTo(TecnicoAsignado::class, 'id_tecnico_asignado');
    }
}
