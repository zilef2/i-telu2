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

    public function getCreatedAtAttribute()
    {
        return date('d-m-Y H:i', strtotime($this->attributes['created_at']));
    }

    public function getUpdatedAtAttribute()
    {
        return date('d-m-Y H:i', strtotime($this->attributes['updated_at']));
    }

    public function getEmailVerifiedAtAttribute()
    {
        return $this->attributes['email_verified_at'] == null ? null : date('d-m-Y H:i', strtotime($this->attributes['email_verified_at']));
    }

    public function getPermissionArray()
    {
        return $this->getAllPermissions()->mapWithKeys(function ($pr) {
            return [$pr['name'] => true];
        });
    }

    public function reportes()
    {
        return $this->hasMany('App\Models\Reporte');
    }


    public function universidades(): BelongsToMany
    {
        return $this->BelongsToMany(Universidad::class);
    }
    public function carreras(): BelongsToMany
    {
        return $this->BelongsToMany(Carrera::class);
    }
    public function materias(): BelongsToMany
    {
        return $this->BelongsToMany(Materia::class);
    }
    public function unidads()
    {
        $result = $this->materias->flatMap(function ($materia) {
            return collect($materia->unidads);
        });
        // dd($result);
        return $result;
    }

    public function trabajadorsSinUniversidad($elrol)
    {
        $this->doesntHave('universidad')
            ->WhereHas('roles', function ($query) use ($elrol) {
                $query->where('name', $elrol);
            })
            ->get();
    }
<<<<<<< HEAD

    public function LosPromps() { return $this->belongsToMany(LosPromps::class ); }


=======
>>>>>>> a3a47f4b68ef3f01c9a880a3ed85bb7aff8eb3ae
}
