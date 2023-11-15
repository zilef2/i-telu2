<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Materia extends Model {
    use HasFactory;

    protected $fillable = [
        'nombre',
        'descripcion',
        'carrera_id',

        'justificacion',
        'codigo',
        'enum',

        //1 octubre
        'activa', //Esta materia puede seguir recibiendo modificaciones y unidades
    ];


    //<editor-fold desc="Mandatory relationships">
    public function unidads(): HasMany {return $this->hasMany(Unidad::class, 'materia_id');}
    public function archivos(): HasMany {return $this->hasMany(Archivo::class, 'materia_id');}
    public function Tsubtemas(): HasManyThrough {
        return $this->hasManyThrough(Subtopico::class, Unidad::class);
    }

    public function objetivos(): HasMany {
        return $this->hasMany(Objetivo::class, 'materia_id');
    }
    public function carrera(): BelongsTo {
        return $this->belongsTo(Carrera::class, 'carrera_id');
    }

    // requisitos
    public function requisito1(): BelongsTo {
        return $this->belongsTo(Materia::class, 'req1_materia_id');
    }
    public function requisito2(): BelongsTo {
        return $this->belongsTo(Materia::class, 'req2_materia_id');
    }
    public function requisito3(): BelongsTo {
        return $this->belongsTo(Materia::class, 'req3_materia_id');
    }
    public function users(): BelongsToMany {
        return $this->BelongsToMany(User::class);
    } //$materias->users

    //</editor-fold>


    public function universidad()
    {
        return $this->carrera->universidad;
    }


    //<editor-fold desc="return a String">
    public function objetivosString($cuantos = false) {
        $pluc = $this->objetivos->pluck('nombre')->toArray();
        $cuantosObjetivosTiene = count($pluc);
        if($cuantosObjetivosTiene === 0)
            return 'Sin objetivos';

        if($cuantos) return $cuantosObjetivosTiene;
        return implode(". ", $pluc);
    }
    public function requisito1_nombre() {
        return $this->requisito1 != null ? $this->requisito1->nombre : '';
    }


    public function requisito2_nombre() {
        return $this->requisito2 != null ? $this->requisito2->nombre : '';
    }

    public function requisito3_nombre() {
        return $this->requisito3 != null ? $this->requisito3->nombre : '';
    }

    //fin requisitos
    public function carrera_nombre(): string {
        return $this->carrera->nombre;
    }



    public function users_nombres(): string {
        $usuarios = $this->users->pluck('name')->toArray();
        if (count($usuarios) === 0)
            return "Sin usuarios asociados";
        else
            return implode(",", $usuarios);
    }
    public function users_data($data): string {
        $usuarios = $this->users->pluck($data)->toArray();
        if (count($usuarios) === 0)
            return "Sin resultados";
        else
            return $usuarios;
    }
    //</editor-fold>


    public function usuarios($materiaid, $elrol) {
        $result = $this->belongsToMany(User::class, 'materia_user')
            ->wherePivot('materia_id',$materiaid)
            ->WhereHas('roles',function ($query) use ($elrol){
                $query->where('name', $elrol );
            });

        return $result;
    }
}
