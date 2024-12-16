<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TecnicoAsignado extends Model
{
    use SoftDeletes;

    protected $table = 'tecnico_asignados';

    protected $fillable = [
        'id_usuario',
        'id_ticket'
    ];

    public function bitacoras()
    {
        return $this->hasOne(BitacoraTicket::class, 'id_tecnico_asignado');
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
