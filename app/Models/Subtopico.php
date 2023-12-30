<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subtopico extends Model
{
    use HasFactory,SoftDeletes;
    protected $fillable = [
        'nombre',
        'descripcion',
        'unidad_id',
        'resultado_aprendizaje',
        'codigo',
        'enum',
    ];
    public function ejercicios(): HasMany
    {
        return $this->hasMany(Ejercicio::class, 'subtopico_id');
    }

    public function Unidad(): BelongsTo
    {
        return $this->belongsTo(Unidad::class, 'unidad_id');
    }
    public function tema_nombre(): string
    {
        return $this->Unidad->nombre;
    }

    public function find_carrera_nombre(): string
    {
        // return $this->Unidad->nombre;
        return $this->Unidad->materia->carrera->nombre;
    }
}
