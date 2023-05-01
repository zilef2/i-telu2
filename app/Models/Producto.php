<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'codigo',
        'precio',
        'cantidad',
        'observaciones',
        'centrotrabajo_id',
    ];

    public function centro() { return $this->belongsToMany('App\Models\CentroTrabajo'); }
    
}
