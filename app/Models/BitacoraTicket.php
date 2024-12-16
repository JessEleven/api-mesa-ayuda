<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BitacoraTicket extends Model
{
    use SoftDeletes;

    protected $table = 'bitacora_tickets';

    protected $fillable = [
        'descripcion',
        'id_tecnico_asignado'
    ];

    public function tecnico_asigados()
    {
        return $this->belongsTo(TecnicoAsignado::class, 'id_tecnico_asignado');
    }
}
