<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Universidad extends Model {
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'enum',
        'nombre',
        'codigo',
    ];

    public function carreras(): HasMany { return $this->hasMany(Carrera::class, 'universidad_id'); }
    public function materiasInscritas() { return $this->hasManyThrough(Materia::class, Carrera::class, 'universidad_id', 'carrera_id'); }

    public function users() {
        return $this->belongsToMany(User::class, 'universidad_user');
    }
    public function estudiantes($universidadid, $inscrito, $elrol) {
        if ($inscrito) {
            $result = $this->belongsToMany(User::class, 'universidad_user')
            ->wherePivot('universidad_id',$universidadid)
            ->WhereHas('roles',function ($query) use ($elrol){
                $query->where('name', $elrol );
            });

        } else {
            $result = $this->belongsToMany(User::class, 'universidad_user')
            ->wherePivot('universidad_id','<>',$universidadid)
            ->WhereHas('roles',function ($query) use ($elrol){
                $query->where('name', $elrol );
            });
        }

        return $result;
    }

    public function estudiantesMuchosRoles($universidadid, $inscrito, $roles) {
        if ($inscrito) {
            $result = $this->belongsToMany(User::class, 'universidad_user')
            ->wherePivot('universidad_id',$universidadid)
            ->WhereHas('roles',function ($query) use ($roles){
                $query->whereIn('name', $roles );
            });

        } else {
            $result = $this->belongsToMany(User::class, 'universidad_user')
            ->wherePivot('universidad_id','<>',$universidadid)
            ->WhereHas('roles',function ($query) use ($roles){
                $query->whereIn('name', $roles );
            });
        }

        return $result;
    }

}
