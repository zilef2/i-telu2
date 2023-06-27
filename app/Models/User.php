<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

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
        'limite_token_general',
        'limite_token_leccion',
        
        'posicion_id',
        'pgrado', //bachiller, pregrado, postgrado
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

    public function getCreatedAtAttribute() {
        return date('d-m-Y H:i', strtotime($this->attributes['created_at']));
    }

    public function getUpdatedAtAttribute() {
        return date('d-m-Y H:i', strtotime($this->attributes['updated_at']));
    }

    public function getEmailVerifiedAtAttribute() {
        return $this->attributes['email_verified_at'] == null ? null:date('d-m-Y H:i', strtotime($this->attributes['email_verified_at']));
    }

    public function getPermissionArray() {
        return $this->getAllPermissions()->mapWithKeys(function ($pr) {
            return [$pr['name'] => true];
        });
    }

    public function reportes() {
		return $this->hasMany('App\Models\Reporte');
	}
    

    public function materias(): BelongsToMany { return $this->BelongsToMany(Materia::class); }
    public function carreras(): BelongsToMany { return $this->BelongsToMany(Carrera::class); }
    public function universidades(): BelongsToMany { return $this->BelongsToMany(Universidad::class); }
}
