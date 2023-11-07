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
        'valorIA',
        'argumentoIA',

        'valor_Resumen',
        'valor_Introduccion',
        'valor_Discusion',
        'valor_Conclusiones',
        'valor_Metodologia',

        'tokens',

        'libre_id',
        'Modelo_de_libre',

        'user_id',
        'QuienCalifico',

        'UniCarreraMateria',
        'UniCarreraMateriaID',
    ];

    public function user() { return $this->belongsTo(User::class, 'user_id'); }
    public function user_name(): string {
        return $this->user->name;
    }
    public function PromedioValores(): float {
        return
            ($this->valor + $this->valorIA)/2;
    }

}
