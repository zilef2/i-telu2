<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Universidad extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre'
    ];

    public function carreras(): HasMany {
        return $this->hasMany(Carrera::class, 'universidad_id');
    }
    public function materiasInscritas() {

        return $this->hasManyThrough(Materia::class, Carrera::class, 'universidad_id', 'carrera_id');
    }

    public function users() {
        return $this->belongsToMany(User::class, 'universidad_user');
    }
}
