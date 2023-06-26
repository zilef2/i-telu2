<?php

namespace App\helpers;

// use Illuminate\Support\Facades\Auth;
// use Illuminate\Support\Facades\Log;

// use Hamcrest\Type\IsInteger;

class HelpGPT{

    const respuestaLimite = 'Limite de tokens';
    const respuestaLarga = 'La respuesta es demasiado extensa';
    const MAX_USAGE_RESPUESTA = 550;
    const MAX_USAGE_TOTAL = 600;

    
    public static function ApartarSujerencias($respuestaGPT,$plantillaPracticar, $pp, &$finishingReason) {
            $vectorEjercicios = explode("\n", $respuestaGPT);
            $vectorEjercicios = array_filter($vectorEjercicios, 'trim');
    
            // $vectorEjercicios = [
            //     1 => "La energía cinética y potencial son dos formas de energía cruciales para la física y deben ser entendidas. La energía cinética es la energía que un objeto posee cuando está en movimiento, mientras que la energía potencial es la energía que se almacena en el objeto gracias a las fuerzas que actúan sobre él. ◀La energía cinética y potencial son dos formas de energía cruciales para la física y deben ser entendidas. La energía cinética es la energía que un objeto posee ",
            //     3 => "Para calcular la energía potencial de un cuerpo con masa 1 kg a una altura de 2 metros, se debe utilizar la ecuación de la energía potencial gravitacional, es decir, U = mgh, donde m es la masa, g es la aceleración de la gravedad y h es la altura. Por lo tanto, la energía potencial es igual a 2 kg × 10 m/s2 × 2 m, lo que equivale a 40 Joules. ◀Para calcular la energía potencial de un cuerpo con masa 1 kg a una altura de 2 metros, se debe utilizar la ecuación de la energía potencial gravitacional, es d ",
            //     5 => "Ejercicios para practicar:",
            //     7 => "1. Calcular la energía cinética de un objeto con masa de 1 kg que se mueve a una velocidad de 5 m/s.",
            //     9 => "2. Calcular la energía potencial de un objeto con masa de 10 kg a una altura de 50 metros.",
            //     11 => "3. ¿Qué es una ecuación de energía cinética? ¿Y una ecuación de energía potencial? Diferencia las dos ecuaciones.",
            // ];
    
            // $posicionEjercicios = -1;
            // foreach ($vectorEjercicios as $key => $value) {
            //     if(strpos($value, 'Ejercicios') !== false){
            //         $posicionEjercicios = $key;
            //     }
            // }
    
            // $posicionEjercicios = array_search($plantillaPracticar.': ',$vectorEjercicios,true);
            $posicionEjercicios2 = array_search($plantillaPracticar.':',$vectorEjercicios,true);
            
            if($posicionEjercicios2 !== false) {
                $contador = $posicionEjercicios2;
                while($contador <= array_key_last($vectorEjercicios)){
                    if($vectorEjercicios[$contador] ?? false){
                        $soloEjercicios[] = $vectorEjercicios[$contador];
                    }
                    $contador++;
                    if($contador > 25)break;
                }
            }else{
                $soloEjercicios = ['Sin sugerencias'];
            }
            $finishingReason = $pp[0]["finish_reason"];
    
            session(['tresEjercicios' => $soloEjercicios]);
            return $soloEjercicios;
        }//fin: ApartarSujerencias
    public static function CalcularTokenConsumidos($usageRespuesta,$usageRespuestaTotal) {

        $restarAlToken = 1;
        $usageRespuesta -= self::MAX_USAGE_RESPUESTA;
        $usageRespuestaTotal -= self::MAX_USAGE_TOTAL;

        while($usageRespuesta > 0 && $usageRespuestaTotal > 0){
            $usageRespuesta -= self::MAX_USAGE_RESPUESTA;
            $usageRespuestaTotal -= self::MAX_USAGE_TOTAL;
            $restarAlToken ++;
        }
        return $restarAlToken;
    }



}