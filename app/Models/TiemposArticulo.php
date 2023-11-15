<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TiemposArticulo extends Model
{
    use HasFactory;

    protected $fillable = [
        'startTime',
        'endTime',
        'tiempoEscritura',
        'huellaArticulo',
        'user_id',
        'articulo_id',
    ];
}
