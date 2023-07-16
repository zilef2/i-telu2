<?php

namespace App\helpers;

use App\Models\Parametro;
use OpenAI;

// use Illuminate\Support\Facades\Auth;
// use Illuminate\Support\Facades\Log;

// use Hamcrest\Type\IsInteger;

class HelpGPT
{


    const respuestaLimite = 'Limite de tokens';
    const respuestaLarga = 'La respuesta es demasiado extensa';
    const servicioNOdisponible = 'servicioNOdisponible';
    const GPTdesabilitado = 'GPTdesabilitado';
    const PreguntaCorta = 'PreguntaCorta';
    const MAX_USAGE_RESPUESTA = 550;
    const MAX_USAGE_TOTAL = 600;



    //usado para sacar los ejercicios que traer GPT y ponerlos en un vector
    public static function ApartarSujerencias($respuestaGPT, $plantillaPracticar)
    {
        $vectorEjercicios = explode("\n", $respuestaGPT);
        $vectorEjercicios = array_filter($vectorEjercicios, 'trim');

        $posicionEjercicios = false;
        // $posicionEjercicios = array_search($plantillaPracticar,$vectorEjercicios,false);

        foreach ($vectorEjercicios as $key => $value) {
            $buscando = strpos($plantillaPracticar . ':', trim($value), false);
            if ($buscando !== false || $buscando === 0) {
                $posicionEjercicios = $key;
                break;
            }
        }

        if ($posicionEjercicios !== false) {
            $contador = $posicionEjercicios;
            while ($contador <= array_key_last($vectorEjercicios)) {
                if ($vectorEjercicios[$contador] ?? false) {
                    $soloEjercicios[] = trim($vectorEjercicios[$contador]);
                }
                $contador++;
                if ($contador > 25) break;
            }
        } else {
            $soloEjercicios = ['Sin sugerencias'];
        }
        session(['tresEjercicios' => $soloEjercicios]);

        return $soloEjercicios;
    } //fin: ApartarSujerencia



    private static function modificarPromt($materia_nombre, $pregunta, $nivel, $plantillaPracticar) {
        $Lapromt = Parametro::first()->prompEjercicios;

        $Lapromt = str_replace("(materia_nombre)", $materia_nombre, $Lapromt);
        $Lapromt = str_replace("(pregunta)", $pregunta, $Lapromt);
        $Lapromt = str_replace("(nivel)", $nivel, $Lapromt);
        $Lapromt = str_replace("(plantillaPracticar)", $plantillaPracticar, $Lapromt);
        return ($Lapromt);
    }
    private static function modificarPSubTema($Lapromt, $materia_nombre, $Unidad, $nivel) {
        // $Lapromt = Parametro::first()->prompExplicarTema;

        $Lapromt = str_replace("[asignatura]", $materia_nombre, $Lapromt);
        $Lapromt = str_replace("[tema]", $Unidad, $Lapromt);
        $Lapromt = str_replace("[nivel de habilidad del estudiante tema]", $nivel, $Lapromt);

        $Lapromt = str_replace("(materia_nombre)", $materia_nombre, $Lapromt);
        $Lapromt = str_replace("(Unidad)", $Unidad, $Lapromt);
        $Lapromt = str_replace("(nivel)", $nivel, $Lapromt);
        return ($Lapromt);
    }

    public static function gptPart1($pregunta, $nivel, $materia_nombre, $usuario, &$soloEjercicios, $debug = false) {

        $longuitudPregunta = strlen($pregunta) > 15;

        if ($longuitudPregunta) {
            if (!$debug) {
                $plantillaPracticar = 'Ejercicios para practicar:';
                $client = OpenAI::client(env('GTP_SELECT'));
                $result = $client->completions()->create([
                    'model' => 'text-davinci-003',
                    'prompt' => self::modificarPromt($materia_nombre, $pregunta, $nivel, $plantillaPracticar),
                    'max_tokens' => HelpGPT::maxToken() // 900 or 500
                ]);
                $respuesta = $result['choices'][0]["text"];
                $finishReason = $result['choices'][0];
                $finishingReason = $finishReason["finish_reason"] ?? '';

                if ($finishingReason == 'stop') {

                    $usageRespuesta = intval($result['usage']["completion_tokens"]); //~ 260
                    $usageRespuestaTotal = intval($result['usage']["total_tokens"]); //~ 500

                    $restarAlToken = HelpGPT::CalcularTokenConsumidos($usageRespuesta, $usageRespuestaTotal);
                    $usuario->update(['limite_token_leccion' => (intval($usuario->limite_token_leccion)) - $restarAlToken]);

                    $soloEjercicios = HelpGPT::ApartarSujerencias($respuesta, $plantillaPracticar);
                    return [$respuesta, $restarAlToken];
                } else {
                    if ($finishingReason == 'length') {
                        return [self::respuestaLarga, 0];
                    } else {
                        return [self::servicioNOdisponible, 0];
                    }
                }
            }

            $respuesta = "El ATP es un compuesto importante en nuestro cuerpo ya que es la principal fuente de energía para todos los procesos metabólicos. Es una molécula con una configuración específica hecha de fosfato, nitrógeno y un anillo de purina. Esto significa que hay otras moléculas similares que contienen el mismo tipo de configuración. Algunos ejemplos son ADP (Adenosina Desfosfato), AMP (Adenosina Monofosfato) y NADH (Nico-Adenosina Deshidrogenasa). Estas moléculas similares se usan como fuente de energía para la mayoría de los procesos metabólicos, de modo que se asemejan a la energía proporcionada por el ATP. 
            Ejercicios para practicar: 
            1.¿Qué compuestos se unen para formar el ATP? 
            2. ¿Qué es la nico-adenosina deshidrogenasa? 
            3. ¿Qué rol juega el ATP en nuestro cuerpo?
            ";
            return [$respuesta, 0];
        }
        return [self::PreguntaCorta, 0];
    }

    public static function gptResolverTema(&$elpromp, $subtopico, $nivel, $materia_nombre, $usuario, $debug = false)
    {
        $longuitudPregunta = strlen($subtopico) > 3;

        $elpromp = self::modificarPSubTema($elpromp,$materia_nombre, $subtopico, $nivel);
        if ($longuitudPregunta) {
            if (!$debug) {
                $client = OpenAI::client(env('GTP_SELECT'));
                // dd($elpromp);
                $result = $client->completions()->create([
                    'model' => 'text-davinci-003',
                    'prompt' => $elpromp,
                    'max_tokens' => HelpGPT::maxToken() // 900.prod or 500.env
                ]);
                $respuesta = $result['choices'][0]["text"];
                $finishReason = $result['choices'][0];
                $finishingReason = $finishReason["finish_reason"] ?? '';

                if ($finishingReason == 'stop') {
                    $usageRespuesta = intval($result['usage']["completion_tokens"]); //~ 260
                    $usageRespuestaTotal = intval($result['usage']["total_tokens"]); //~ 500

                    $restarAlToken = HelpGPT::CalcularTokenConsumidos($usageRespuesta, $usageRespuestaTotal);
                    $usuario->update(['limite_token_leccion' => (intval($usuario->limite_token_leccion)) - $restarAlToken]);

                    return [$respuesta, $restarAlToken];
                } else {
                    if ($finishingReason == 'length') {
                        return [self::respuestaLarga, 0];
                    } else {
                        return ['El servicio no esta disponible', 0];
                    }
                }
            } //debug
            $respuesta = "La energía cinética es un tipo de energía mecánica que se genera cuando un cuerpo se encuentra en movimiento. Esta energía se manifiesta en forma de calor, luz, sonido y movimiento. La energía cinética también se conoce como energía del movimiento, ya que el movimiento mismo es energía que se genera cuando un cuerpo se desplaza.
                En un nivel universitario, la energía cinética se explica a través de la ley de conservación de la energía mecánica. Esta ley establece que la energía mecánica es la misma antes y después del movimiento, a menos que se transfiera a otra forma, como el calor. La energía cinética se calcula mediante la fórmula de energía cinética, que establece que la energía cinética (K) es igual a la mitad del producto de la masa del objeto multiplicado por el cuadrado de su velocidad. En conclusión, la energía cinética es un tipo de energía mecánica generada por el movimiento de los cuerpos. Esta energía se puede calcular usando la ley de conservación de la energía mecánica y se puede manifestar como calor, luz, sonido y movimiento.
            ";
            $finishingReason = 'stop';
            $usageRespuesta = 260;
            $usageRespuestaTotal = 500;
            return [$respuesta, 0];
        }
        return ['El Subtema es demasiado corto', 0];
    }
    public static function CalcularTokenConsumidos($usageRespuesta, $usageRespuestaTotal)
    {
        $restarAlToken = 1;
        $usageRespuesta -= self::MAX_USAGE_RESPUESTA;
        $usageRespuestaTotal -= self::MAX_USAGE_TOTAL;

        while ($usageRespuesta > 0 && $usageRespuestaTotal > 0) {
            $usageRespuesta -= self::MAX_USAGE_RESPUESTA;
            $usageRespuestaTotal -= self::MAX_USAGE_TOTAL;
            $restarAlToken++;
        }
        return $restarAlToken;
    }

    public static function maxToken()
    {
        if (config('app.env') === 'production') {
            return 900; // Adjust the response length as needed
        }
        return 700; // Adjust the response length as needed
    }

    public static function nivelesAplicativo() {
        $niveles = [
            'Primaria',
            'Bachillerato',
            'Tecnologia',
            'Profesional',
            'Especializacion',
            'Maestría',
            'Doctorado'
        ];

        foreach ($niveles as $key => $value) {
            $result[] = ['label' => $value, 'value' => $key];
        }
        return [
            $niveles,
            $result
        ];
    }
    public static function turnInSelectBegin1($theArrayofStrings) {
        foreach ($theArrayofStrings as $key => $value) {
            $result[] = ['label' => $value, 'value' => ($key+1)];
        }
        return $result;
    }
    public static function turnInSelectID($theArrayofStrings) {
        foreach ($theArrayofStrings as $key => $value) {
            $result[] = ['label' => $value->principal, 'value' => ($value->id)];
        }
        return $result;
    }
}
