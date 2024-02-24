<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Carrera extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'nombre',
        'descripcion',

        'perfil_profesional',
        'perfil_ocupacional',

        'universidad_id',
        'codigo',
        'enum',
    ];

    /**
     * Get the universidad that owns the Carrera
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function universidad(): BelongsTo { return $this->belongsTo(Universidad::class, 'universidad_id'); }
    public function materias(): HasMany { return $this->hasMany(Materia::class, 'carrera_id'); }
    public function users() { return $this->belongsToMany(User::class, 'carrera_user'); }

    //fin requisitos
    public function universidad_nombre(): string { return $this->universidad->nombre; }
    public function materias_enum() {
        return $this->materias()->orderBy('enum');
    }

    public function estudiantesMuchosRoles($carreraid, $inscrito, $roles) {
        if ($inscrito) {
            $operacion = '=';
        } else{
            $operacion = '<>';
        }

        $result = $this->belongsToMany(User::class, 'carrera_user')
        ->wherePivot('carrera_id', $operacion, $carreraid)
        ->WhereHas('roles',function ($query) use ($roles){
            $query->whereIn('name', $roles );
        });

        return $result;
    }

    public function EnLaCarreraYaTieneEseNombre($carreraid,$nombreMateria): int {

        $materiasDeLaCarrera = Materia::Where('carrera_id',$this->id);
        return $materiasDeLaCarrera->Where('nombre','like',$nombreMateria)->count();
    }
}
