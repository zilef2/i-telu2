<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Materia extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'descripcion',
        'carrera_id',
    ];

    /**
     * Get all of the temas for the Materia
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function temas(): HasMany
    {
        return $this->hasMany(Tema::class, 'materia_id');
    }

    public function carrera(): BelongsTo
    {
        return $this->belongsTo(Carrera::class, 'carrera_id');
    }
}
