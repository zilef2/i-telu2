<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RespuestaEjercicio extends Model
{
    use HasFactory;

    protected $fillable = [
        'guardar_pregunta',
        'respuesta',
        'nivel',
        'precisa',
        'idExistente',
    ];
}
