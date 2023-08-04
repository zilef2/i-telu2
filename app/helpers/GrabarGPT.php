<?php

namespace App\helpers;

use App\Models\RespuestaEjercicio;

// use Illuminate\Support\Facades\Auth;
// use Illuminate\Support\Facades\Log;

// use Hamcrest\Type\IsInteger;

class GrabarGPT {
    const respuestaLimite = 'Limite de tokens';
    const respuestaLarga = 'La respuesta es demasiado extensa';
    const servicioNOdisponible = 'servicioNOdisponible';
    const GPTdesabilitado = 'GPTdesabilitado';
    const PreguntaCorta = 'PreguntaCorta';
    const MAX_USAGE_RESPUESTA = 550;
    const MAX_USAGE_TOTAL = 600;


    //usado para sacar los ejercicios que traer GPT y ponerlos en un vector
    public static function EncontroONull($collection){

        if($collection){
            if(count($collection) > 0) return $collection->first();
            else return 0;
        }else{
            return null;
        }
    }

    public static function BuscarPromp($StringPregunta) {
        $encontroExacto = RespuestaEjercicio::Where('guardar_pregunta', $StringPregunta);
        $exacto = self::EncontroONull($encontroExacto);
        dd($exacto);
        return $exacto->respuesta;
        
        $encontroSimilar = RespuestaEjercicio::Where('guardar_pregunta', 'LIKE', "%" . $StringPregunta . "%");
        $parecido = self::EncontroONull($encontroSimilar);

        return $parecido->respuesta;


    }
}