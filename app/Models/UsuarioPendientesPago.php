<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class UsuarioPendientesPago extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'fecha_peticion',
        'fecha_aprovacion',
        'valorTotal',
        'tokensComprados',

        'user_id',
        'plan_id',
    ];


    public function user(): BelongsTo {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function nombre_user()
    {
        if($this->user()->get()){
            return $this->user()->get()->first()->name;
        }
        return 'No hay persona asignada';
    }

}
