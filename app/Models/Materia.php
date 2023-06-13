<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Materia extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'descripcion',
        'carrera_id',
    ];

    public function temas(): HasMany {
        return $this->hasMany(Tema::class, 'materia_id');
    }
    public function objetivos(): HasMany {
        return $this->hasMany(Objetivo::class, 'materia_id');
    }

    public function carrera(): BelongsTo {
        return $this->belongsTo(Carrera::class, 'carrera_id');
    }
    public function carrera_nombre(): string {
        return $this->carrera->nombre;
    }

    public function users(): BelongsToMany {
        return $this->BelongsToMany(User::class);
    }//$materias->users

    public function users_nombres(): string {
        $usuarios = $this->users->pluck('name')->toArray();
        if(count($usuarios) === 0)
            return "Sin usuarios asociados";
        else
            return implode(",",$usuarios);
    }
}
