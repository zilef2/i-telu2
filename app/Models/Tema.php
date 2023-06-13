<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tema extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'descripcion',
        'materia_id',
    ];

    public function subtopicos(): HasMany {
        return $this->hasMany(Subtopico::class, 'tema_id');
    }

    public function materia(): BelongsTo {
        return $this->belongsTo(Materia::class, 'materia_id');
    }
    public function materia_nombre(): string {
        return $this->materia->nombre;
    }
}
