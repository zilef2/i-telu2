<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ejercicio extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'descripcion',
        'seHaPreguntado', //1jul2023
        'subtopico_id',
    ];

    public function subtopico(): BelongsTo {
        return $this->belongsTo(Subtopico::class, 'subtopico_id');
    }
    public function subtopico_nombre(): string {
        return $this->subtopico->nombre;
    }
}
