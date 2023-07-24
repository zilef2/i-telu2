<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Objetivo extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'descripcion',
        'materia_id',
    ];

    public function materia(): BelongsTo {
        return $this->belongsTo(Materia::class, 'materia_id');
    }
}
