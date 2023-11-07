<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',

        'identificacion',
        'sexo',
        'fecha_nacimiento',
        'semestre',
        'semestre_mas_bajo',
        'limite_token_general',
        'limite_token_leccion', //zona asignatura

        'pgrado', //bachiller, pregrado, postgrado

        'email_verified_at',
        'plan',
    ];
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function getCreatedAtAttribute() { return date('d-m-Y H:i', strtotime($this->attributes['created_at'])); }

    public function getUpdatedAtAttribute() { return date('d-m-Y H:i', strtotime($this->attributes['updated_at'])); }

    public function getEmailVerifiedAtAttribute() { return $this->attributes['email_verified_at'] == null ? null : date('d-m-Y H:i', strtotime($this->attributes['email_verified_at'])); }
    public function getPermissionArray() {
        return $this->getAllPermissions()->mapWithKeys(function ($pr) {
            return [$pr['name'] => true];
        });
    }
    public function LimiteDePromps() {
        return $this->hasMany('App\Models\LosPromps');
    }

    public function reportes() {
        return $this->hasMany('App\Models\Reporte');
    }

    public function universidades(): BelongsToMany {
        return $this->BelongsToMany(Universidad::class);
    }
    public function carreras(): BelongsToMany {
        return $this->BelongsToMany(Carrera::class);
    }
    public function materias(): BelongsToMany {
        return $this->BelongsToMany(Materia::class);
    }
    public function MedidaControl(): HasMany {
        return $this->HasMany(MedidaControl::class);
    }
    public function articulos(): HasMany {
        return $this->HasMany(Articulo::class);
    }

    public function unidads() {
        $result = $this->materias->flatMap(function ($materia) {
            return collect($materia->unidads);
        });
        return $result;
    }

    public function estudiantesSinUniversidad($elrol) {
        $this->doesntHave('universidad')
            ->WhereHas('roles', function ($query) use ($elrol) {
                $query->where('name', $elrol);
            })
            ->get();
    }

    public function LosPromps() {
        return $this->belongsToMany(LosPromps::class);
    }

    public function EstudiantesDelProfe() {

        $materias = $this->materias;

        foreach ($materias as $key => $value) {
            $matrizMateriasEstudiantes[$value->id] = $value->usuarios($value->id,'estudiante')->get();
        }

        return $matrizMateriasEstudiantes;
    }
}
