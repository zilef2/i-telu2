<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Articulo extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'nick',
        'version',
        'Portada',
        'Resumen', // Revisar sujerencias
        'Resumen_ia',
        'Resumen_final',
        'Palabras_Clave',
        'Introduccion',// Revisar sujerencias
        'Introduccion_ia',
        'Introduccion_final',
        'Revision_de_la_Literatura',
        'Metodologia',// Revisar sujerencias
        'Metodologia_ia',// Revisar sujerencias
        'Metodologia_final',// Revisar sujerencias
        'Resultados',
        'Discusion',// Revisar sujerencias
        'Discusion_ia',// Revisar sujerencias
        'Discusion_final',// Revisar sujerencias
        'Conclusiones',// Revisar sujerencias
        'Conclusiones_ia',// Revisar sujerencias
        'Conclusiones_final',// Revisar sujerencias
        'Agradecimientos',
        'Referencias',
        'Anexos_o_Apendices',
        'user_id',
        'universidad_id',
        'carrera_id',
        'materia_id',
        'libre_id',
        'Modelo_de_libre',

        'Resumen_integer',
        'Introduccion_integer',
        'Discusion_integer',
        'Conclusiones_integer',
        'Metodologia_integer',

        'Resumen_critica',
        'Introduccion_critica',
        'Discusion_critica',
        'Conclusiones_critica',
        'Metodologia_critica',

        'tipo',
        'Critica_string',
        'Referencias_ai',
        'links_ai',

        'Resumen_correcciones',
        'Introduccion_correcciones',
        'Metodologia_correcciones',
        'Discusion_correcciones',
        'Conclusiones_correcciones',
    ];

    protected $totalPreguntas = ['total'];

    public function calculateTotal(){
        $this->attributes['total'] =
            $this->Resumen_integer +
            $this->Introduccion_integer +
            $this->Discusion_integer +
            $this->Conclusiones_integer +
            $this->Metodologia_integer;
    }

    public function user() { return $this->belongsTo(User::class, 'user_id'); }
    public function user_name(): string {
        return $this->user->name;
    }
    public function calificacion() { return $this->hasMany(Calificacion::class, 'libre_id'); }
    public function calificacion_name(): float {
        $calif = $this->calificacion->Where('Modelo_de_libre',"articulo_id");
        if($calif && $calif->count() > 0 && $calif->first()->valor) {
            return $calif->first()->valor;
        }
        return 0;
    }
    public function calificacion_IA(): float {
        $calif = $this->calificacion->Where('Modelo_de_libre',"articulo_id");
        if($calif && $calif->count() > 0 && $calif->first()->valorIA) {
            return $calif->first()->valorIA;
        }
        return 0;
    }

    public function PromedioValores() : float
    {
        if($this->calificacion && $this->calificacion->first())
            return $this->calificacion->first()->PromedioValores();
        return 0;
    }
}
