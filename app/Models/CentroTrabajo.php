<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CentroTrabajo extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        // 'horas_trabajadas',//admit null
	];
    // public function centro() { return $this->belongsToMany('App\Models\CentroCosto'); }
    public function productos() { return $this->HasMany('App\Models\Produto'); }
}
