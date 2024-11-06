<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoriaTicket extends Model
{
    protected $table = 'categoria_tickets';

    protected $fillable = [
        'nombre_categoria'
    ];

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'id_categoria');
    }
}
