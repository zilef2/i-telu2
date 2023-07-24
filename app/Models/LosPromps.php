<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LosPromps extends Model
{
    use HasFactory;

    protected $fillable = [
        'principal',
        'teoricaOpractica',
        'clasificacion',
        'tokensAproximados',
    ];


    public function subtopicos() { return $this->belongsToMany(Subtopico::class ); }
    public function users() { return $this->belongsToMany(User::class ); }
}

