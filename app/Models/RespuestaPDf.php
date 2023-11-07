<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @method static create(array $array)
 */
class RespuestaPDf extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'guardar_pdf',
        'resumen',
        'nivel',
        'precisa',
        'idExistente',
    ];
}
