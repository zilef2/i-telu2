<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RespuestaPDf extends Model
{
    use HasFactory;

    protected $fillable = [
        'guardar_pdf',
        'resumen',
        'nivel',
        'precisa',
        'idExistente',
    ];
}
