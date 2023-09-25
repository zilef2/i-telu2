<?php

namespace App\helpers;

use App\Models\MedidaControl;
use App\Models\RespuestaEjercicio;
use App\Models\RespuestaPDf;
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
    private static function EncontroONull($collection){
        if($collection){
            if(($collection->count()) > 0) return $collection->first()->respuesta;
            else{
                // MedidaControl::create([
                //     'pregunta' => '',
                //     'respuesta_guardada' => '',
                //     'RazonNOSubtopico' => 'No encontro ejercicios',
                //     'subtopico_id' => 0,
                //     'tokens_usados' => 0,
                //     'user_id' => Auth::user()->id
                // ]);
                return null;
            }
        }else{
            return null;
        }
    }
    private static function EncontroONullPDF($collection){
        if($collection){
            if(($collection->count()) > 0) return $collection->first()->resumen;
            else{
                return null;
            }
        }else{
            return null;
        }
    }
    //# END private zone



    public static function BuscarPromp($StringPregunta) {
        $presicion = 4;
        $EncontroONull = null;
        while($EncontroONull == null){

            $encontroExacto = RespuestaEjercicio::Where('guardar_pregunta', $StringPregunta)->Where('precisa',$presicion);
            $EncontroONull = self::EncontroONull($encontroExacto);
            // if($EncontroONull !== null) return $EncontroONull;

            $numberPermission = Myhelp::getPermissionToNumber();
            if($numberPermission === 1)
            $StringPregunta = 'Solo estudiante. '.$StringPregunta;

            $encontroExacto = RespuestaEjercicio::Where('guardar_pregunta', $StringPregunta)->Where('precisa',$presicion);
            // return self::EncontroONull($encontroExacto);
            
            //todo: using?
            $encontroSimilar = RespuestaEjercicio::Where('guardar_pregunta', 'LIKE', "%" . $StringPregunta . "%")->Where('precisa',$presicion);
            
            $presicion--;
            if($presicion == 2) return null;
        }
        return self::EncontroONull($encontroSimilar);
    }


    public static function BuscarPDFPromt($StringPregunta) {
        $presicion = 4;
        $EncontroONull = null;
        while($EncontroONull == null){

            $encontroExacto = RespuestaPDf::Where('guardar_pdf', $StringPregunta)
            // ->Where('precisa',$presicion)
            ;
            $EncontroONull = self::EncontroONullPDF($encontroExacto);
            if($EncontroONull) return $EncontroONull;

            $numberPermission = Myhelp::getPermissionToNumber();
            if($numberPermission === 1)
            $StringPregunta = 'Solo estudiante. '.$StringPregunta;

            $encontroExacto = RespuestaPDf::Where('guardar_pdf', $StringPregunta)->Where('precisa',$presicion);
            // return self::EncontroONullPDF($encontroExacto);
            
            $encontroSimilar = RespuestaPDf::Where('guardar_pdf', 'LIKE', "%" . $StringPregunta . "%")->Where('precisa',$presicion);
            
            $presicion--;
            if($presicion == 2) return null;
        }
        return self::EncontroONullPDF($encontroSimilar);
    }
}