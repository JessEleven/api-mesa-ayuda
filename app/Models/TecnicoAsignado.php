<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class TecnicoAsignado extends Model
{
    protected $table = 'tecnicos_asignados';

    protected $fillable = [
        'id_usuario',
        'id_ticket'
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

    // Accesor para registro_eliminado
    public function getRecursoEliminadoAttribute($value)
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
