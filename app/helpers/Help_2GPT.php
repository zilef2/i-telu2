<?php

namespace App\helpers;

class Help_2GPT {
    const respuestaLimite = 'Limite de tokens';
    const respuestaLarga = 'La respuesta es demasiado extensa';
    const servicioNOdisponible = 'servicioNOdisponible';
    const GPTdesabilitado = 'GPTdesabilitado';
    const PreguntaCorta = 'PreguntaCorta';
    const MAX_USAGE_RESPUESTA = 550;
    const MAX_USAGE_TOTAL = 600;

    //usado cuando se generan las unidades y temas de una materia
    public static function PostRespuestaIA($result,$usuario) {
        $respuesta = $result['choices'][0]["text"];
        $finishReason = $result['choices'][0];
        $finishingReason = $finishReason["finish_reason"] ?? '';

        if ($finishingReason == 'stop') {
            $usageRespuesta = intval($result['usage']["completion_tokens"]); //~ 260
            $usageRespuestaTotal = intval($result['usage']["total_tokens"]); //~ 500

            $restarAlToken = HelpGPT::CalcularTokenConsumidos($usageRespuesta, $usageRespuestaTotal);

            $totalTokens = (intval($usuario->limite_token_leccion)) - $restarAlToken;
            $totalTokens = $totalTokens < 0 ? 0 : $totalTokens;
            $usuario->update(['limite_token_leccion' => $totalTokens]);

           
            //$soloEjercicios = HelpGPT::ApartarSujerencias($respuesta, $plantillaPracticar);
            return [ 'respuesta' => $respuesta, 'restarAlToken' => $restarAlToken, 'funciono' => true];
        } else {
            if ($finishingReason == 'length') {
                return [ 'respuesta' => self::respuestaLarga, 'restarAlToken' => 0, 'funciono' => false];
            } else {
                return [ 'respuesta' => self::servicioNOdisponible, 'restarAlToken' => 0, 'funciono' => false];
            }
        }
    }


    public static function BorrarEspaciosDelArrayRespuesta(&$Array) {

        unset($Array[0]);
        $pattern = '/^\d+\.\s/';

        foreach ($Array as $key => $value) {

            $value = str_replace('  ','',$value);
            
            if($value == '.') unset($Array[$key]);
            else if($value == '')  unset($Array[$key]);


            $vectorLetras = [
                '',
                'A','B','C','D',
                'E','F','G','H',
                'I','J','K','L',
                'M','N','O','P',
                'Q','R','S','T',

                'a','b','c','d',
                'e','f','g','h',
                'i','j','k','l',
                'm','n','o','p',
            ];//todo: calcular cuanto es el maximo de temas que se generarian

            for ($i=35; $i > 0; $i--) {
                $value = str_replace('. ','',$value);
                $value = str_replace('.','',$value);
                $value = str_replace(':','',$value);

                $value = str_replace($i,'',$value);
                $value = str_replace($i.')','',$value);
                
                $value = str_replace($vectorLetras[$i].')  ','',$value);
                $value = str_replace($vectorLetras[$i].') ','',$value);
                $value = str_replace($vectorLetras[$i].')','',$value);
            }

            $palabrasBorradas = [
                'Renglon',
                'Renglón',
                'RENGLON',
                'renglon',
                'Resultado de Aprendizaje ',
                'Resultado de aprendizaje ',
                'resultado de aprendizaje ',
                'Tema ',
                'tema ',
                'Unidad  ',
                'unidad  ',
            ];

            foreach ($palabrasBorradas as $palabra) {
                $value = str_replace($palabra,'',$value);
            }

            $Array[$key] = preg_replace($pattern, '', $value);
            if(strlen($value) < 3) unset($Array[$key]);
        }
        $Array = array_combine(range(1, count($Array)), $Array);
    }

    public static function Materias_Unidades_Temas(&$ArrayRespuesta,$numeroRenglones) {

        $ArrayRespuesta['respuesta'] = explode("\n",$ArrayRespuesta['respuesta']);

       self::BorrarEspaciosDelArrayRespuesta($ArrayRespuesta['respuesta']);

        $ArrayRespuesta['funciono'] = 
            (count($ArrayRespuesta['respuesta']) == $numeroRenglones)
            ||
            (count($ArrayRespuesta['respuesta']) == $numeroRenglones+1)
            ||
            (count($ArrayRespuesta['respuesta'])+1 == $numeroRenglones)
            ;
    }
    
}