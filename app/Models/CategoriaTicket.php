<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class CategoriaTicket extends Model
{
    protected $table = 'categorias_tickets';

    protected $fillable = [
        'nombre_categoria'
    ];

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'id_categoria');
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
