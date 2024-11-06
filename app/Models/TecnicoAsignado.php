<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TecnicoAsignado extends Model
{
    protected $table = 'tecnico_asignados';

    protected $fillable = [
        'estado_eliminado',
        'fecha_registro',
        'id_usuario',
        'id_ticket'
    ];

    public function bitacoras()
    {
        return $this->hasMany(BitacoraTicket::class, 'id_tecnico_asignado');
    }

    public function usuarios()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }

    public function tickets()
    {
        return $this->belongsTo(Ticket::class, 'id_ticket');
    }
}
