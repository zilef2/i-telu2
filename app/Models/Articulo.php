<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Articulo extends Model
{
    use HasFactory;

    protected $fillable = [
        'nick',
        'version',
        'Portada',
        'Resumen',
        'Palabras_Clave',
        'Introduccion',
        'Revision_de_la_Literatura',
        'Metodologia',
        'Resultados',
        'Discusion',
        'Conclusiones',
        'Agradecimientos',
        'Referencias',
        'Anexos_o_Apendices',
        'user_id' //user_id
    ];

    public function user() { return $this->belongsTo(User::class, 'user_id'); }


    public function user_name(): string {
        return $this->user->name;
    }

}
