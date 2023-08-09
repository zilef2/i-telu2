<?php

namespace App\helpers;

use App\Models\MedidaControl;
use App\Models\RespuestaEjercicio;
use Illuminate\Support\Facades\Auth;

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
            if(($collection->count()) > 0) return $collection->first()->respuesta;
            else{
                MedidaControl::create([
                    'tokens_usados' => 0,
                    'user_id' => Auth::user()->id
                ]);
                return '';
            }
        }else{
            return null;
        }
    }

    public static function BuscarPromp($StringPregunta) {

        $encontroExacto = RespuestaEjercicio::Where('guardar_pregunta', $StringPregunta);
        $EncontroONull = self::EncontroONull($encontroExacto);
        if($EncontroONull !== null) return $EncontroONull;

        $numberPermission = Myhelp::getPermissionToNumber();
        if($numberPermission === 1)
        $StringPregunta = 'Solo estudiante. '.$StringPregunta;

        $encontroExacto = RespuestaEjercicio::Where('guardar_pregunta', $StringPregunta);
        return self::EncontroONull($encontroExacto);
        
        //todo: using?
        $encontroSimilar = RespuestaEjercicio::Where('guardar_pregunta', 'LIKE', "%" . $StringPregunta . "%");
        return self::EncontroONull($encontroSimilar);
    }
}