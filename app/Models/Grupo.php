<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Grupo extends Model{

    use HasFactory,SoftDeletes;

    protected $fillable = [
        'nombre',
        'codigo',
        'descripcion',
        'materia_id',
        'enum',
    ];

    //<editor-fold desc="Mandatory relationships">
    public function materia(): BelongsTo {return $this->Belongsto(Materia::class, 'materia_id');}

    public function users(): BelongsToMany {
        return $this->BelongsToMany(User::class);
    }

    //</editor-fold>


}
