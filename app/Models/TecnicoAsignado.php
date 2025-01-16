<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class TecnicoAsignado extends Model
{
    use SoftDeletes;

    protected $table = 'tecnicos_asignados';

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
