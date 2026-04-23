<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Destino extends Model
{
    protected $table = 'destinos';
    protected $fillable = [
        'nombre_dest', 
        'pais_dest', 
        'dias_dest',
        'descripcion_dest',
        'cupos_dest',
        'paquete_dest',
        'precio_dest',
        'imagen_dest',
    ];

    public function reservas() {
        return $this->hasMany(Reserva::class, 'destino_id');
    }
}

