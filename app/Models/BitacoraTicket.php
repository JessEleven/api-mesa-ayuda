<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class BitacoraTicket extends Model
{
    use SoftDeletes;

    protected $table = 'bitacoras_tickets';

    protected $fillable = [
        'descripcion',
        'id_tecnico_asignado'
    ];

    public function tecnico_asigados()
    {
        return $this->belongsTo(TecnicoAsignado::class, 'id_tecnico_asignado');
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
