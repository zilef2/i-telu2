<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cuota extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'numeroDeLaCuota',
        'numeroDecuotas',
        'valor',

        'usuario_pendientes_pago_id',
    ];
}
