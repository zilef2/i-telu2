<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RespuestaEjercicio extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'guardar_pregunta',
        'respuesta',
        'nivel',
        'precisa',
        'idExistente',
    ];
}
