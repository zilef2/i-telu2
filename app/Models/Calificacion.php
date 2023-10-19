<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Calificacion extends Model
{
    use HasFactory;

    protected $fillable = [
        'TipoPrueba',
        'prompUsado',
        'valor',
        'tokens',
        'CualUniCarreraMateriaID',
        'UniCarreraMateriaID',
        'QuienCalifico',

        'valor_Resumen',
        'valor_Introduccion',
        'valor_Discusion',
        'valor_Conclusiones',
        'valor_Metodologia',
        
        'user_id'
    ];

    public function user() { return $this->belongsTo(User::class, 'user_id'); }
    public function user_name(): string {
        return $this->user->name;
    }
}
