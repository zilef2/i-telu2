<?php

namespace App\helpers;

use App\Models\MedidaControl;
use App\Models\Parametro;
use App\Models\RespuestaEjercicio;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
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
    const TOKEN_GENERAR_MATERIA = 2000;


    //? goes to materiascontroller.index
    public static function ValoresGenerarMateria($stringCarreraNombre, $materiaNombre, $numero, $debug = false)
    {
        if (!$debug) {
            $renglon = 2;
            // $listMaterias = implode(", ", $listaMaterias->toArray());

            $unidades = intval($numero['unidades']);
            $temas = intval($numero['temas']);
            $numeroRenglones = 1 + $unidades + ($temas * 2 * $unidades);

            for ($i = 0; $i < $unidades; $i++) {

                $renglonesUnidad[$i] = 2 + $i * (2 * $temas + 1);
                for ($j = 0; $j < $temas * 2; $j++) {
                    $renglonesTema[] = $renglonesUnidad[$i] + ($j + 1);
                    $j++;
                    $renglonesRA[] = $renglonesUnidad[$i] + ($j + 1);
                }
            }
            $renglonesUnidad = implode(", ", $renglonesUnidad);
            $renglonesTema = implode(", ", $renglonesTema);
            $renglonesRA = implode(", ", $renglonesRA);

            $elpromp =
                "Actua como un rector universitario. Las asignaturas tienen muchas unidades, las unidades tienen muchos temas y un tema tiene un resultado de aprendizaje"
                . " para este caso se piden " . $unidades . " unidades y " . $temas . " temas"
                . " Divide la respuesta en  $numeroRenglones renglones, con el siguiente patron"
                // ". En el renglon 1 Genera el nombre de una asignatura que se encuentre en la carrera universitaria :".$stringCarreraNombre. 
                // ". No puede ser una de las siguientes materias: ".$listMaterias.
                . ". En el renglon 1 genera el objetivo de la asignatura " . $materiaNombre
                . ". En los renglones " . $renglonesUnidad . " genera el nombre de una unidad que pertenesca a dicha asignatura"
                . ". En los renglones  " . $renglonesTema . " genera los temas respectivos que pertenescan a las unidades del siguiente modo."
                . ". En los renglones  " . $renglonesRA . " genera los resultados de aprendizaje respectivos que del tema."
                . " si el numero de temas es " . $temas . " cada unidad tendrá " . $temas . " temas exactamente"
                . ". Adicionalmente, si el numero de temas es " . $temas . " cada unidad tendrá " . $temas . " resultados de aprendizaje exactamente";

            $client = OpenAI::client(env('GTP_SELECT'));

            // $result = $client->chat()->create([
            //     "model" => "gpt-4",
            //     'messages' => [
            //         ['role' => 'system', 'content' => 'Eres un profesor universitario con 20 años de experiencia'],
            //         ['role' => 'user', 'content' => $elpromp],
            //     ],
            //     'max_tokens' => HelpGPT::maxTokenPDF()
            // ]);

            $result = $client->completions()->create([
                'model' => 'text-davinci-003',
                'prompt' => $elpromp,
                'max_tokens' => self::TOKEN_GENERAR_MATERIA
            ]);

            $usuario = Auth::user();
            $ArrayRespuesta = Help_2GPT::PostRespuestaIADavinci($result, $usuario);
            if ($ArrayRespuesta['funciono']) {

                MedidaControl::create([
                    'pregunta' => $ArrayRespuesta['respuesta'],
                    'respuesta_guardada' => '',
                    'subtopico_id' => null, // 1 ocasion en la que el subtopico es null
                    'RazonNOSubtopico' => 'Generó unidades y temas',

                    'tokens_usados' => $ArrayRespuesta['restarAlToken'],
                    'user_id' => $usuario->id
                ]);

                $ArrayRespuesta['Cuantas_unidades'] = $unidades;
                $ArrayRespuesta['Cuantas_temas'] = $temas;
                Help_2GPT::Materias_Unidades_Temas($ArrayRespuesta, $numeroRenglones);
            }
            array_unshift($ArrayRespuesta['respuesta'], $materiaNombre);
            return $ArrayRespuesta;
        }

        $ArrayRespuesta['respuesta'] = [
            1 => "Objetivo: Comprender el concepto básico del movimiento lineal y sus leyes ",
            2 => "Unidad 1: Movimiento y Leyes de Newton",
            3 => "Tema: Siguientes Leyes de Newton",
            4 => "Resultado de aprendizaje: Interpretar los conceptos fundamentales de la Mecánica Clásica para aplicar las Leyes de Newton al movimiento lineal de los cuerpos.",
        ];
        $ArrayRespuesta['Cuantas_unidades'] = 1;
        $ArrayRespuesta['Cuantas_temas'] = 1;
        $ArrayRespuesta['restarAlToken'] = 0;
        $ArrayRespuesta['funciono'] = true;
        array_unshift($ArrayRespuesta['respuesta'], $materiaNombre);
        return $ArrayRespuesta;
    }
    //usado para sacar los ejercicios que traer GPT y ponerlos en un vector
    public static function ApartarSujerencias($respuestaGPT, $plantillaPracticar)
    {
        $vectorEjercicios = explode("\n", $respuestaGPT);
        $vectorEjercicios = array_filter($vectorEjercicios, 'trim');

        $posicionEjercicios = false;
        // $posicionEjercicios = array_search($plantillaPracticar,$vectorEjercicios,false);

        foreach ($vectorEjercicios as $key => $value) {
            $buscando = strpos($plantillaPracticar, trim($value), false);
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
    } //fin: Apartarujerencia

    //todo: strpos(stringGrande, buscado) -> $buscando = strpos

    private static function ApartarChuleta($respuestaGPT, $plantillaPracticar) {
        $vectorChuleta = explode("\n", $respuestaGPT);

        //asegurar que el primer renglon, sea el titulo
        if (strpos(trim($vectorChuleta[0]), '¿'))
            array_unshift($vectorChuleta, "Quiz");

        $ArrayRespuestasCorrectas = [];
        $posicionInicial = 0;

        $contador = 0;
        //todo: usar array_filter
        foreach ($vectorChuleta as $key => $value) {
            if (strlen(trim($value)) > 1) {
                $NuevoVectorChuleta[$contador] = $value;
                $contador++;
            }
        }
        //buscar donde hay preguntas
        foreach ($NuevoVectorChuleta as $key => $value) {
            $buscando = strpos(trim($value), $plantillaPracticar);
            if ($buscando !== false) {
                $posicionPreguntas[] = $posicionInicial;
                $ArrayRespuestasCorrectas[] = $key;
                $posicionInicial += $key;
            }
        }

        //si no devuelve un vector, la IA no respondio bien
        if (count($ArrayRespuestasCorrectas) < 1) return [
            'vectorChuleta' => [],
            'ArrayRespuestasCorrectas' => 'Formato de respuesta invalido',
            'ArrayPreguntas' => ''
        ];

        //guardar las posiciones de dichas preguntas
        $posicionInicial = 0;
        foreach ($ArrayRespuestasCorrectas as $key => $correcta) {
            for ($i = $posicionInicial; $i < $correcta; $i++) {
                $ArrayPreguntas[$key][] = $i;
            }
            $posicionInicial = $correcta;
        }

        return [
            'vectorChuleta' => $NuevoVectorChuleta,
            'ArrayRespuestasCorrectas' => $ArrayRespuestasCorrectas,
            'ArrayPreguntas' => $ArrayPreguntas
        ];
    } //fin: Apartarujerencia



    private static function modificarPromt($materia_nombre, $pregunta, $nivel, $plantillaPracticar)
    {
        $Lapromt = Parametro::first()->prompEjercicios;

        $Lapromt = str_replace("(materia_nombre)", $materia_nombre, $Lapromt);
        $Lapromt = str_replace("(pregunta)", $pregunta, $Lapromt);
        $Lapromt = str_replace("(nivel)", $nivel, $Lapromt);
        $Lapromt = str_replace("(plantillaPracticar)", $plantillaPracticar, $Lapromt);
        return ($Lapromt);
    }

    public static function contarModificarP(&$Lapromt, $materia_nombre = '', $subtopico = '', $Unidad = '', $nivel = '', $carrera_nombre = '')
    {

        $Lapromt = strtolower($Lapromt);
        $myhelp = new Myhelp();

        $contadorC = 0;
        $contadorP = 0;
        if ($nivel === '') {
            $nivel = 'profesional';
        }

        $remplazarPofavo = [
            // 'Asignatura' => $materia_nombre,
            'asignatura' => $materia_nombre,
            'materia_nombre' => $materia_nombre,
            'materia' => $materia_nombre,
            // 'Materia' => $materia_nombre,

            'concepto que se enseña' => $Unidad,
            // 'Concepto que se enseña' => $Unidad,

            // 'Tema' => $subtopico,
            'tema' => $subtopico,

            // 'Unidad' => $Unidad,
            'unidad' => $Unidad,

            'nivel' => $nivel,

            'carrera_nombre' => $carrera_nombre,
            'carrera' => $carrera_nombre,
        ];

        foreach ($remplazarPofavo as $key => $value) {
            $keyflexible = strtolower($key);
            $corchetes = "[" . $keyflexible . "]";
            $parentesis = "(" . $keyflexible . ")";

            $ArrayCorche = $myhelp->EncontrarEnString($Lapromt, $corchetes);
            $contadorC += count($ArrayCorche);
            $ArrayParent = $myhelp->EncontrarEnString($Lapromt, $parentesis);
            $contadorP += count($ArrayParent);

            if ($contadorC !== 0) {
                $Lapromt = str_replace($corchetes, strtolower($value), strtolower($Lapromt));
            }
            if ($contadorP !== 0) {
                $Lapromt = str_replace($parentesis, strtolower($value), strtolower($Lapromt));
            }
        }

        foreach ($remplazarPofavo as $key => $value) {
            $keyflexible = ucfirst(strtolower($key));
            $corchetes = "[" . $keyflexible . "]";
            $parentesis = "(" . $keyflexible . ")";

            $ArrayCorche = $myhelp->EncontrarEnString($Lapromt, $corchetes);
            $contadorC += count($ArrayCorche);
            $ArrayParent = $myhelp->EncontrarEnString($Lapromt, $parentesis);
            $contadorP += count($ArrayParent);

            if ($contadorC !== 0) {
                $Lapromt = str_replace($corchetes, strtolower($value), strtolower($Lapromt));
            }
            if ($contadorP !== 0) {
                $Lapromt = str_replace($parentesis, strtolower($value), strtolower($Lapromt));
            }
        }
        return ['corchetes' => $contadorC, 'parentesis' => $contadorP];
    }


    //EXCLUSIVO se usa para resolver ejercicios
    public static function gptPart1($ModelEjercicio, $nivel, $materia_nombre, $usuario, &$soloEjercicios, $debug = false)
    {
        $pregunta = $ModelEjercicio->nombre;

        $longuitudPregunta = strlen($pregunta) > 7;

        if ($longuitudPregunta) {
            if (!$debug) {
                $plantillaPracticar = 'Ejercicios para practicar:';
                $client = OpenAI::client(env('GTP_SELECT'));
                $elpromp = self::modificarPromt($materia_nombre, $pregunta, $nivel, $plantillaPracticar);
                $result = $client->completions()->create([
                    'model' => 'text-davinci-003',
                    'prompt' => $elpromp,
                    'max_tokens' => HelpGPT::maxToken()
                ]);
                $respuesta = $result['choices'][0]["text"];
                $finishReason = $result['choices'][0];
                $finishingReason = $finishReason["finish_reason"] ?? '';

                if ($finishingReason == 'stop') {
                    $usageRespuesta = intval($result['usage']["completion_tokens"]); //~ 260
                    $usageRespuestaTotal = intval($result['usage']["total_tokens"]); //~ 500

                    $restarAlToken = HelpGPT::CalcularTokenConsumidos($usageRespuesta, $usageRespuestaTotal);
                    $usuario->update(['limite_token_leccion' => (intval($usuario->limite_token_leccion)) - $restarAlToken]);

                    MedidaControl::create([
                        'pregunta' => $elpromp,
                        'respuesta_guardada' => $respuesta,
                        'subtopico_id' => $ModelEjercicio->subtopico_id,
                        'tokens_usados' => $restarAlToken,
                        'user_id' => $usuario->id
                    ]);
                    $soloEjercicios = HelpGPT::ApartarSujerencias($respuesta, $plantillaPracticar);
                    return ['respuesta' => $respuesta, 'restarAlToken' => $restarAlToken];
                } else {
                    if ($finishingReason == 'length') {
                        return ['respuesta' => self::respuestaLarga, 'restarAlToken' => 0];
                    } else {
                        return ['respuesta' => self::servicioNOdisponible, 'restarAlToken' => 0];
                    }
                }
            }
            $respuesta = "El ATP es un compuesto importante en nuestro cuerpo ya que es la principal fuente de energía para todos los procesos metabólicos. Es una molécula con una configuración específica hecha de fosfato, nitrógeno y un anillo de purina. Esto significa que hay otras moléculas similares que contienen el mismo tipo de configuración. Algunos ejemplos son ADP (Adenosina Desfosfato), AMP (Adenosina Monofosfato) y NADH (Nico-Adenosina Deshidrogenasa). Estas moléculas similares se usan como fuente de energía para la mayoría de los procesos metabólicos, de modo que se asemejan a la energía proporcionada por el ATP. 
                Ejercicios para practicar: 
                1.¿Qué compuestos se unen para formar el ATP? 
                2. ¿Qué es la nico-adenosina deshidrogenasa? 
                3. ¿Qué rol juega el ATP en nuestro cuerpo?
            ";
            return ['respuesta' => $respuesta, 'restarAlToken' => 0];
        }
        return ['respuesta' => self::PreguntaCorta, 'restarAlToken' => 0];
    }

    public static function gptResolverQuiz(&$elpromp, $subtopico, $nivel, $materia_nombre, $usuario, $debug = false)
    {

        //contarModificarP cambia [tema] = el tema seleccionado
        $carrera_Nombre = $subtopico->find_carrera_nombre();

        $corchetesYparentesis = self::contarModificarP($elpromp, $materia_nombre, $subtopico, $nivel, $carrera_Nombre);
        //todo: si corchetesYparentesis estan en cero, no debe continuar

        $elpromp .= ". Al final de las opciones, imprime la respuesta correcta por cada pregunta con este formato: RESPUESTA=A";
        $elpromp .= ". Cada opcion, debe ocupar una fila y deben ser 4 opciones de la A a la D";
        $elpromp .= ". La primera fila debe tener un titulo relacionado a lo que se evalua y la segunda fila debe ser la primera pregunta.";
        $elpromp = str_replace("..", ".", $elpromp);


        $longuitudPregunta = strlen($subtopico) > 3;
        if ($longuitudPregunta) {
            // if ($debug) {
            if (!$debug) { //this one is ok

                $client = OpenAI::client(env('GTP_SELECT'));
                $result = $client->completions()->create([
                    'model' => 'text-davinci-003',
                    'prompt' => $elpromp,
                    'max_tokens' => HelpGPT::maxToken()
                ]);
                $respuesta = $result['choices'][0]["text"];
                $finishReason = $result['choices'][0];
                $finishingReason = $finishReason["finish_reason"] ?? '';

                if ($finishingReason == 'stop') {
                    $usageRespuesta = intval($result['usage']["completion_tokens"]); //~ 260
                    $usageRespuestaTotal = intval($result['usage']["total_tokens"]); //~ 500

                    $chuleta = self::ApartarChuleta($respuesta, 'RESPUESTA=');

                    $restarAlToken = HelpGPT::CalcularTokenConsumidos($usageRespuesta, $usageRespuestaTotal);

                    $tokensAntes = intval($usuario->limite_token_leccion);
                    $usuario->update(['limite_token_leccion' => ($tokensAntes) - $restarAlToken]);

                    $respuestaImplode = implode(':|:', $chuleta['ArrayPreguntas']);

                    MedidaControl::create([
                        'pregunta' => $elpromp,
                        'respuesta_guardada' => $respuestaImplode,
                        'subtopico_id' => $subtopico->id,
                        'tokens_usados' => $restarAlToken,
                        'user_id' => $usuario->id
                    ]);

                    return [
                        'vectorChuleta' => $chuleta['vectorChuleta'],
                        'ArrayPreguntas' => $chuleta['ArrayPreguntas'],
                        'ArrayRespuestasCorrectas' => $chuleta['ArrayRespuestasCorrectas'],
                        'restarAlToken' => $restarAlToken,
                    ];
                } else {
                    if ($finishingReason == 'length') {
                        MedidaControl::create([
                            'pregunta' => $elpromp,
                            'respuesta_guardada' => 'RESPUESTA_MUY_LARGA',
                            'subtopico_id' => $subtopico->id,
                            'tokens_usados' => 0, 'user_id' => $usuario->id
                        ]);
                        return [
                            'vectorChuleta' => [self::respuestaLarga],
                            'ArrayPreguntas' => [],
                            'ArrayRespuestasCorrectas' => [],
                            'restarAlToken' => 0,
                        ];
                    } else {
                        MedidaControl::create([
                            'pregunta' => $elpromp,
                            'respuesta_guardada' => 'SERVICIO_GPT_NO_DISPONIBLE',
                            'subtopico_id' => $subtopico->id,
                            'tokens_usados' => 0, 'user_id' => $usuario->id
                        ]);
                        return [
                            'vectorChuleta' => ['El servicio no esta disponible'],
                            'ArrayPreguntas' => [],
                            'ArrayRespuestasCorrectas' => [],
                            'restarAlToken' => 0,
                        ];
                    }
                }
            }

            //debug
            $respuesta = "
                \n
                1. ¿Cual es la Definicion de un limite?
                A. Un limite se refiere a la frontera o extremo máximo de algo 
                B. El limite es una palabra usada para agregar numero a otro 
                C. El limite es un medio de transporte 
                D. El limite es un signo de aritmética 
                
                RESPUESTA=A
                
                2. ¿Cuál de las siguientes es un ejemplo de un límite?
                A. 35 + 129 
                B. 34 grados 
                C. El límite de velocidad es 55 mph
                D. Toma dos horas terminar
                
                RESPUESTA=C
                
                3. ¿En qué contexto puede haber un límite?
                A. Descubriendo una constelación
                B. Cocinando un plato 
                C. Entramando una historia
                D. Vendiendo un coche
                
                RESPUESTA=D
                
                4. ¿Por qué los límites son importantes?
                A. Porque dan seguridad
                B. Porque conducen a la creatividad 
                C. Porque te ayudan a planificar mejor tu tiempo 
                D. Porque los límites ayudan a definir relaciones
                
                RESPUESTA=C
                
                5. ¿Por qué los límites cambian?
                A. Porque son dinámicos
                B. Porque se vuelven más estrictos 
                C. Porque hay cambios en la tecnología
                D. Porque hay una nueva ley
                
                RESPUESTA=A
            ";
            $chuleta = self::ApartarChuleta($respuesta, 'RESPUESTA');

            MedidaControl::create([
                'pregunta' => $elpromp,
                'respuesta_guardada' => 'RESPUESTA_DEBUG | ' . $respuesta,
                'subtopico_id' => $subtopico->id,
                'tokens_usados' => 0, 'user_id' => $usuario->id
            ]);
            $finishingReason = 'stop';
            $usageRespuesta = 260;
            $usageRespuestaTotal = 500;
            return [
                'vectorChuleta' => $chuleta['vectorChuleta'],
                'ArrayPreguntas' => $chuleta['ArrayPreguntas'],
                'ArrayRespuestasCorrectas' => $chuleta['ArrayRespuestasCorrectas'],
                'restarAlToken' => 0,
            ];
        }
        return [
            'vectorChuleta' => ['El Subtema es demasiado corto'],
            'ArrayPreguntas' => [],
            'ArrayRespuestasCorrectas' => [],
            'restarAlToken' => 0,
        ];
    }
    public static function gptResolverTema(&$elpromp, $subtopico, $unidad, $nivel, $materia_nombre, $usuario, $debug = false)
    {
        try {
            $longuitudPregunta = strlen($subtopico->nombre) > 3;
            $carrera_Nombre = $subtopico->find_carrera_nombre();
            self::contarModificarP($elpromp, $materia_nombre, $subtopico->nombre, $unidad, $nivel, $carrera_Nombre);

            //# buscando el prompt
            $YaEstabaGuardada = GrabarGPT::BuscarPromp($elpromp);
            if ($YaEstabaGuardada && $YaEstabaGuardada !== '') {
                MedidaControl::create([
                    'pregunta' => $elpromp,
                    'subtopico_id' => $subtopico->id,
                    'respuesta_guardada' => $YaEstabaGuardada,
                    'tokens_usados' => 0, 'user_id' => $usuario->id,
                ]);
                return ['respuesta' => $YaEstabaGuardada, 'restarAlToken' => 0];
            }

            //todo: 
            // $numberPermission = Myhelp::getPermissionToNumber();
            // if($numberPermission === 1)
            // return [ 'respuesta' => env('NOTVALIDATEDBYTEACHER'), 'restarAlToken' => 0];

            if ($longuitudPregunta) {
                if (!$debug) {

                    $ChatR = JustChatFunctionGPT::Chat($elpromp);

                    $respuesta = $ChatR[1];
                    $finishingReason = $ChatR[2];
                    if ($finishingReason == 'stop') {

                        $restarAlToken = $ChatR[0];
                        $TokensRestantes = (intval($usuario->limite_token_leccion)) - $restarAlToken;
                        $TokensRestantes = $TokensRestantes >= 0 ? $TokensRestantes : 0;
                        $usuario->update(['limite_token_leccion' => $TokensRestantes]);

                        MedidaControl::create([
                            'pregunta' => $elpromp,
                            'respuesta_guardada' => $respuesta,
                            'subtopico_id' => $subtopico->id,
                            'tokens_usados' => $restarAlToken,
                            'user_id' => $usuario->id
                        ]);

                        //todo: si esprofesor tendra mucho mas peso, que si es alumno //todo: deberia guardar, pero si es profesor, sobreescribe //todo: pero por ahora, solo guarda cuando es profesor o mas // if($numberPermission === 1) // $respuesta = 'Solo estudiante. '.$respuesta;
                        $numberPermissions = Myhelp::getPermissionToNumber(auth()->user()->roles->pluck('name')[0]);
                        $precisa = $numberPermissions == 3 || $numberPermissions > 8 ? 4 : 3;
                        RespuestaEjercicio::create([
                            'guardar_pregunta' => $elpromp,
                            'respuesta' => $respuesta,
                            'nivel' => $nivel,
                            'precisa' => $precisa, //0 (nada preciso) - 5 (muy preciso)
                            'idExistente' => null,
                        ]);

                        return ['respuesta' => $respuesta, 'restarAlToken' => $restarAlToken];
                    } else {
                        if ($finishingReason == 'length') {
                            MedidaControl::create([
                                'pregunta' => $elpromp,
                                'respuesta_guardada' => 'RESPUESTA_MUY_LARGA',
                                'subtopico_id' => $subtopico->id,
                                'tokens_usados' => 0, 'user_id' => $usuario->id
                            ]);
                            return [
                                'respuesta' => self::respuestaLarga,
                                'restarAlToken' => 0
                            ];
                        } else {
                            MedidaControl::create([
                                'pregunta' => $elpromp,
                                'respuesta_guardada' => 'SERVICIO_GPT_NO_DISPONIBLE',
                                'subtopico_id' => $subtopico->id,
                                'tokens_usados' => 0, 'user_id' => $usuario->id
                            ]);
                            return [
                                'respuesta' => 'El servicio no esta disponible',
                                'restarAlToken' => 0
                            ];
                        }
                    }
                } //debug
                $respuesta = "La energía cinética es un tipo de energía mecánica que se genera cuando un cuerpo se encuentra en movimiento. Esta energía se manifiesta en forma de calor, luz, sonido y movimiento. La energía cinética también se conoce como energía del movimiento, ya que el movimiento mismo es energía que se genera cuando un cuerpo se desplaza.
                    En un nivel universitario, la energía cinética se explica a través de la ley de conservación de la energía mecánica. Esta ley establece que la energía mecánica es la misma antes y después del movimiento, a menos que se transfiera a otra forma, como el calor. La energía cinética se calcula mediante la fórmula de energía cinética, que establece que la energía cinética (K) es igual a la mitad del producto de la masa del objeto multiplicado por el cuadrado de su velocidad. En conclusión, la energía cinética es un tipo de energía mecánica generada por el movimiento de los cuerpos. Esta energía se puede calcular usando la ley de conservación de la energía mecánica y se puede manifestar como calor, luz, sonido y movimiento.
                ";
                $finishingReason = 'stop';
                $usageRespuesta = 260;
                $usageRespuestaTotal = 500;
                MedidaControl::create([
                    'pregunta' => $elpromp,
                    'respuesta_guardada' => 'RESPUESTA_DEBUG | ' . $respuesta,
                    'subtopico_id' => $subtopico->id,
                    'tokens_usados' => 0, 'user_id' => $usuario->id
                ]);
                return [
                    'respuesta' => $respuesta,
                    'restarAlToken' => 0
                ];
            }
            MedidaControl::create([
                'pregunta' => $elpromp,
                'respuesta_guardada' => 'PREGUNTA_MUY_CORTA',
                'subtopico_id' => $subtopico->id,
                'tokens_usados' => 0, 'user_id' => $usuario->id,
            ]);
            return [
                'respuesta' => 'El Subtema es demasiado corto',
                'restarAlToken' => 0
            ];
        } catch (\Throwable $th) {
            $errores =
                $th->getMessage() .
                ' L:' . $th->getLine() . ' Ubi:' . $th->getFile();
            dd($errores);
            Log::alert("U -> " . Auth::user()->name . " fallo en preguntar la IA:  " . $errores);
            return back()->with('error', 'fallo en preguntar la IA: ' . $errores);
        }
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
        $numberPermissions = Myhelp::getPermissionToNumber(auth()->user()->roles->pluck('name')[0]);

        //max 8,192 or | gpt-4-32k 32,768 tokens
        if (config('app.env') === 'production') {
            $maxTokens = 1100 + $numberPermissions * 200; //2000 (10.000 caracteres) + $maxtokens
            return $maxTokens;
        }
        return 3000;
    }

    public static function maxTokenPDF()
    {
        $numberPermissions = Myhelp::getPermissionToNumber(auth()->user()->roles->pluck('name')[0]);

        //max 8,192 or | gpt-4-32k 32,768 tokens
        if (config('app.env') === 'production') {
            $maxTokens = 3100 + $numberPermissions * 200; //2000 (10.000 caracteres) + $maxtokens
            return $maxTokens;
        }
        return 5100;
    }


    public static function nivelesAplicativo()
    {
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
    public static function turnInSelectBegin1($theArrayofStrings)
    {
        foreach ($theArrayofStrings as $key => $value) {
            $result[] = ['label' => $value, 'value' => ($key + 1)];
        }
        return $result;
    }

    public static function turnInSelectID($theArrayofStrings)
    {
        $result = [['label' => 'Selecciona un promp', 'value' => 'a']];
        foreach ($theArrayofStrings as $key => $value) {
            $result[] = ['label' => $value->principal, 'value' => ($value->id), 'tipo' => $value->teoricaOpractica];
            // $ListaPromp = LosPromps::Where('clasificacion','Expectativas Altas')->Where('teoricaOpractica','teorica')->get();
        }
        return $result;
    }

    public static function NEW_turnInSelectID($theArrayofStrings)
    {
        $result = [['title' => 'Selecciona un promp', 'value' => 0, 'tipo' => 'General']];
        foreach ($theArrayofStrings as $key => $value) {
            $result[] = ['title' => $value->principal, 'value' => ($value->id), 'tipo' => $value->teoricaOpractica];
            // $ListaPromp = LosPromps::Where('clasificacion','Expectativas Altas')->Where('teoricaOpractica','teorica')->get();
        }
        return $result;
    }


    //quiz de actionEQH
    public static function gptQuizEstudiante(&$elpromp, $subtopico, $nivel, $materia_nombre, $usuario, $debug = false)
    {
        //contarModificarP cambia [tema] = el tema seleccionado
        $carrera_Nombre = $subtopico->find_carrera_nombre();
        $corchetesYparentesis = self::contarModificarP($elpromp, $materia_nombre, $subtopico->nombre, $nivel, $carrera_Nombre);
        //todo: si corchetesYparentesis estan en cero, no debe continuar

        $elpromp .= ". Al final, imprime la respuesta correcta con este formato: RESPUESTA=A";
        $elpromp .= ". Cada opcion, debe ocupar una fila y deben ser 4 opciones de la A a la D";
        $elpromp .= ". La primera fila debe tener un titulo relacionado a lo que se evalua y la segunda fila debe ser la primera pregunta.";
        $elpromp = str_replace("..", ".", $elpromp);

        $longuitudPregunta = strlen($subtopico->nombre) > 3;
        if ($longuitudPregunta) {
            // if ($debug) {
            if (!$debug) { //this one is ok

                $client = OpenAI::client(env('GTP_SELECT'));
                $result = $client->completions()->create([
                    'model' => 'text-davinci-003',
                    'prompt' => $elpromp,
                    'max_tokens' => HelpGPT::maxToken()
                ]);

                $respuesta = $result['choices'][0]["text"];
                $finishReason = $result['choices'][0];
                $finishingReason = $finishReason["finish_reason"] ?? '';

                if ($finishingReason == 'stop') {
                    $usageRespuesta = intval($result['usage']["completion_tokens"]); //~ 260
                    $usageRespuestaTotal = intval($result['usage']["total_tokens"]); //~ 500
                    $chuleta = self::ApartarChuleta($respuesta, 'RESPUESTA=');
                    $restarAlToken = HelpGPT::CalcularTokenConsumidos($usageRespuesta, $usageRespuestaTotal);

                    $tokensAntes = intval($usuario->limite_token_leccion);
                    $usuario->update(['limite_token_leccion' => ($tokensAntes) - $restarAlToken]);


                    $guardarPreguntas = implode(':|:', $chuleta['vectorChuleta']);
                    MedidaControl::create([
                        'pregunta' => $elpromp,
                        'respuesta_guardada' => $guardarPreguntas,
                        'RazonNOSubtopico' => 'Solicitó un quiz',
                        'subtopico_id' => $subtopico->id,
                        'tokens_usados' => $restarAlToken,
                        'user_id' => $usuario->id
                    ]);

                    return [
                        'vectorChuleta' => $chuleta['vectorChuleta'],
                        'ArrayPreguntas' => $chuleta['ArrayPreguntas'],
                        'ArrayRespuestasCorrectas' => $chuleta['ArrayRespuestasCorrectas'],
                        'restarAlToken' => $restarAlToken,
                    ];
                } else {
                    if ($finishingReason == 'length') {
                        return [
                            'vectorChuleta' => [self::respuestaLarga],
                            'ArrayPreguntas' => [],
                            'ArrayRespuestasCorrectas' => [],
                            'restarAlToken' => 0,
                        ];
                    } else {
                        return [
                            'vectorChuleta' => ['El servicio no esta disponible'],
                            'ArrayPreguntas' => [],
                            'ArrayRespuestasCorrectas' => [],
                            'restarAlToken' => 0,
                        ];
                    }
                }
            }

            //debug
            $respuesta = "
                1. ¿Cual es la Definicion de un limite (debuging)?
                A. Un limite se refiere a la frontera o extremo máximo de algo 
                B. El limite es una palabra usada para agregar numero a otro 
                C. El limite es un medio de transporte 
                D. El limite es un signo de aritmética 
                RESPUESTA=A
            ";
            $chuleta = self::ApartarChuleta($respuesta, 'RESPUESTA');

            $finishingReason = 'stop';
            $usageRespuesta = 260;
            $usageRespuestaTotal = 500;
            return [
                'vectorChuleta' => $chuleta['vectorChuleta'], //array con la respuesta pura de la IA
                'ArrayPreguntas' => $chuleta['ArrayPreguntas'], //keys de la ubicacion de las preguntas
                'ArrayRespuestasCorrectas' => $chuleta['ArrayRespuestasCorrectas'], //keys de la respuesta
                'restarAlToken' => 0,
            ];
        }
        return [
            'vectorChuleta' => ['El Subtema es demasiado corto'],
            'ArrayPreguntas' => [],
            'ArrayRespuestasCorrectas' => [],
            'restarAlToken' => 0,
        ];
    }

    //13/09/2023
    public static function GenerarPreguntaAbierta($materia, $unidad, $tema, $pregunta, $numberPermissions, $carrera_nombre)
    {

        $preguntaAbierta =
            'Actua como un Profesional, experto en la asignatura: ' . $materia . ', el subtema: ' . $tema . ' del tema: ' . $unidad
            . 'Responda la siguiente pregunta de una manera fácil de entender: ' . $pregunta;

        // 'Niégate a responder si la pregunta no esta relacionada con '. $tema .' o ' . $materia . '
        // En caso que te niegues, hazle entender que esto es un aplicativo para la enseñanza académica.

        // 'Responda lo siguiente con el contexto de la asignatura:' . $materia . '. el subtema: ' . $tema . ' del tema: ' . $unidad 
        // . 'el objetivo es explicar el tema en términos fáciles de entender. Esto podría incluir proporcionar instrucciones paso a paso para resolver un problema, sugerir recursos en línea para un estudio más profundo. '
        // . 'La pregunta es: ' . $pregunta . '.'
        // ;

        if ($numberPermissions < 9) {
            $preguntaAbierta .=
                " Si la pregunta no esta relacionada con el tema $tema o con la unidad $unidad o con la asignatura $materia"
                . "Responde un mensaje que le haga entender que se encuentra en un aplicativo para la enseñanza academica.";
        } else {
            $preguntaAbierta .= " Recuerda que solo debes responder preguntas relacionadas con el tema $tema. Pero no seas tan restrictivo";
        }

        $preguntaAbierta .= " Al final, lista una serie de palabras claves (minimo 5).";

        return $preguntaAbierta;
    }

    public static function MedidaGenerarMateria($materia, $ArraySubtopicosModels)
    {
        $user = auth()->user();
        foreach ($ArraySubtopicosModels as $key => $value) {
            // $user->decrement(['limite_token_general']);
            MedidaControl::create([
                'pregunta' => 'generar Materia ' . $materia->nombre,
                'respuesta_guardada' => $materia->id . '',
                'RazonNOSubtopico' => 'Solicitó generarMateria',
                'subtopico_id' => $value->id,
                'tokens_usados' => 1,
                // 'tokens_usados' => count($ArraySubtopicosModels),
                'user_id' => $user->id
            ]);
        }

        $tokensConsumidos = $user->limite_token_general - count($ArraySubtopicosModels);
        $tokensConsumidos = $tokensConsumidos < 0 ? 0 : $tokensConsumidos;
        $user->update([
            'limite_token_general' => $tokensConsumidos
        ]);
    }
}
