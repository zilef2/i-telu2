<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Unidad extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'nombre',
        'descripcion',
        'materia_id',
        'codigo',
        'enum',
    ];

    public function subtopicos(): HasMany
    {
        return $this->hasMany(Subtopico::class, 'unidad_id');
    }

    public function materia(): BelongsTo
    {
        return $this->belongsTo(Materia::class, 'materia_id');
    }
    public function materia_nombre(): string
    {
        return $this->materia->nombre;
    }
}
