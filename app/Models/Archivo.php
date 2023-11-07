<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Archivo extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'nombre',
        'peso',
        'NombreOriginal',
        'type',
        'user_id',
        'materia_id',
        'Resumen1',
        'Resumen2',
        'Resumen3',
        'Resumen4',
        'resumen_2_lineas',
        'aportes',
        'articulosRelacionados',
        'implicacionPracticas',
        'campoAbierto1',
        'campoAbierto2',
        'StringcampoAbierto1',
        'StringcampoAbierto2',
    ];
}
