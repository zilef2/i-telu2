<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subtopico extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'descripcion',
        'tema_id',
    ];

    public function ejercicios(): HasMany {
        return $this->hasMany(ejercicio::class, 'subtopico_id');
    }

    public function tema(): BelongsTo {
        return $this->belongsTo(tema::class, 'tema_id');
    }
    public function tema_nombre(): string {
        return $this->tema->nombre;
    }
}
