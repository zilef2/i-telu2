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

        'objetivo1',
        'objetivo2',
        'objetivo3',

        'req1_materia_id',//requisitos
        'req2_materia_id',
        'req3_materia_id',
    ];

    public function temas(): HasMany {
        return $this->hasMany(Tema::class, 'materia_id');
    }
    public function objetivos(){
        $objetivos = '';
        $objetivos .= $this->objetivo1 != null ? $this->objetivo1 : '';
        $objetivos .= $this->objetivo2 != null ? '. '. $this->objetivo2 : '';
        $objetivos .= $this->objetivo3 != null ? '. '.$this->objetivo3 : '';

        if($objetivos == '')
            return 'No hay objetivos registrados';
        else
            return $objetivos;
    }

    public function carrera(): BelongsTo {
        return $this->belongsTo(Carrera::class, 'carrera_id');
    }

    // requisitos
    public function requisito1(): BelongsTo { return $this->belongsTo(Materia::class, 'req1_materia_id'); }
    public function requisito1_nombre(){ 
        return $this->requisito1 != null ? $this->requisito1->nombre : '';
     }

    public function requisito2(): BelongsTo { return $this->belongsTo(Materia::class, 'req2_materia_id'); }
    public function requisito2_nombre(){ 
        return $this->requisito2 != null ? $this->requisito2->nombre : '';
     }

    public function requisito3(): BelongsTo { return $this->belongsTo(Materia::class, 'req3_materia_id'); }
    public function requisito3_nombre(){ 
        return $this->requisito3 != null ? $this->requisito3->nombre : '';
     }

    //fin requisitos

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
    public function users_data($data): string {
        $usuarios = $this->users->pluck($data)->toArray();
        if(count($usuarios) === 0)
            return "Sin resultados";
        else
            return $usuarios;
    }
}
