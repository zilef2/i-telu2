<?php

namespace App\helpers;

use App\Models\MedidaControl;
use App\Models\Parametro;
use App\Models\RespuestaEjercicio;
use Illuminate\Support\Facades\Auth;
use OpenAI;

// use Illuminate\Support\Facades\Auth;
// use Illuminate\Support\Facades\Log;

// use Hamcrest\Type\IsInteger;

class HelpGPT {
    const respuestaLimite = 'Limite de tokens';
    const respuestaLarga = 'La respuesta es demasiado extensa';
    const servicioNOdisponible = 'servicioNOdisponible';
    const GPTdesabilitado = 'GPTdesabilitado';
    const PreguntaCorta = 'PreguntaCorta';
    const MAX_USAGE_RESPUESTA = 550;
    const MAX_USAGE_TOTAL = 600;
    const TOKEN_GENERAR_MATERIA = 4000;


    //? goes to materiascontroller.index
    public static function ValoresGenerarMateria($stringCarreraNombre,$materiaNombre,$numero, $debug = false) {
        if (!$debug) {
            $renglon = 3;
            // $listMaterias = implode(", ", $listaMaterias->toArray());

            $unidades = intval($numero['unidades']);
            $temas = intval($numero['temas']);
            $numeroRenglones = 1 + $unidades + ($temas * 2 * $unidades);

            for ($i=0; $i < $unidades; $i++) { 

                $renglonesUnidad[$i] = 2 + $i*($temas+1);
                for ($j=0; $j < $temas; $j++) { 
                    $renglonesTema[] = $renglonesUnidad[$i] + ($j+1);
                }
            }
            $renglonesUnidad = implode(", ", $renglonesUnidad);
            // $renglonesTema = implode(", ", $renglonesTema);
            
            $elpromp = 
            "Actua como un rector universitario. Las asignaturas tienen muchas unidades, y las unidades tienen muchos temas."
            ." Divide la respuesta en  $numeroRenglones renglones, con el siguiente patron".
            // ". En el renglon 1 Genera el nombre de una asignatura que se encuentre en la carrera universitaria :".$stringCarreraNombre. 
            // ". No puede ser una de las siguientes materias: ".$listMaterias.
            ". En el renglon 1 genera el objetivo de la asignatura ".$materiaNombre
            .". En el renglon $renglonesUnidad genera el nombre de una unidad que pertenesca a dicha asignatura";
            
            for ($i = 0; $i < $unidades; $i++) {
                $elpromp .= ". En el renglon ".$renglonesTema[$i]." genera el nombre de una unidad que pertenesca a la asignatura "($i + 1);
            }

            // for ($i=0; $i < $numero['unidades']; $i++) { 
            //     $elpromp .= ". En el renglon ".$renglon." genera el nombre de una unidad que pertenesca a dicha asignatura";
            //     $renglon++;
            //     for ($j=0; $j < $numero['temas']; $j++) { 
            //         $elpromp .= ". En el renglon ".$renglon." genera el nombre de un tema que pertenesca a la ultima unidad";
            //         $renglon++;
            //         $elpromp .= ". En el renglon ".$renglon." genera un resultado aprendizaje de este tema";
            //         $renglon++;
            //     }
            // }
            
            $client = OpenAI::client(env('GTP_SELECT'));
            $result = $client->completions()->create([
                'model' => 'text-davinci-003',
                'prompt' => $elpromp,
                'max_tokens' => self::TOKEN_GENERAR_MATERIA
            ]);
            $ArrayRespuesta = Help_2GPT::PostRespuestaIA($result);
            if($ArrayRespuesta['funciono']){
                $ArrayRespuesta['Cuantas_unidades'] = $numero['unidades'];
                $ArrayRespuesta['Cuantas_temas'] = $numero['temas'];
                Help_2GPT::Materias_Unidades_Temas($ArrayRespuesta,$numeroRenglones);
            }

            return $ArrayRespuesta;
        }
        
        $respuesta =[
            2 => "Mecánica clásica",
            3 => "Unidad 1: Movimiento y Leyes de Newton",
            4 => "Objetivo: Comprender el concepto básico del movimiento lineal y sus leyes ",
            5 => "Tema: Siguientes Leyes de Newton",
            6 => "Resultado de aprendizaje: Interpretar los conceptos fundamentales de la Mecánica Clásica para aplicar las Leyes de Newton al movimiento lineal de los cuerpos.",
        ];
        return [ 'respuesta' => $respuesta, 'restarAlToken' => 0, 'funciono' => true];
    }
    //usado para sacar los ejercicios que traer GPT y ponerlos en un vector
    public static function ApartarSujerencias($respuestaGPT, $plantillaPracticar) {
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
        if( strpos(trim($vectorChuleta[0]),'¿') )
            array_unshift($vectorChuleta,"Quiz");

        $ArrayRespuestasCorrectas = [];
        $posicionInicial = 0;

        $contador = 0;
        //todo: usar array_filter
        foreach ($vectorChuleta as $key => $value) {
            if(strlen(trim($value)) > 1){
                $NuevoVectorChuleta[$contador] = $value;
                $contador++;
            }
        }
        //buscar donde hay preguntas
        foreach ($NuevoVectorChuleta as $key => $value) {
            $buscando = strpos(trim($value),$plantillaPracticar);
            if ($buscando !== false ) {
                $posicionPreguntas[] = $posicionInicial;
                $ArrayRespuestasCorrectas[] = $key;
                $posicionInicial += $key;
            }
        }

        //si no devuelve un vector, la IA no respondio bien
        if(count($ArrayRespuestasCorrectas) < 1) return [
            'vectorChuleta' => [],
            'ArrayRespuestasCorrectas' => 'Formato de respuesta invalido',
            'ArrayPreguntas' => ''
        ];

        //guardar las posiciones de dichas preguntas
        $posicionInicial = 0;
        foreach ($ArrayRespuestasCorrectas as $key => $correcta) {
            for ($i=$posicionInicial; $i < $correcta; $i++) { 
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



    private static function modificarPromt($materia_nombre, $pregunta, $nivel, $plantillaPracticar) {
        $Lapromt = Parametro::first()->prompEjercicios;

        $Lapromt = str_replace("(materia_nombre)", $materia_nombre, $Lapromt);
        $Lapromt = str_replace("(pregunta)", $pregunta, $Lapromt);
        $Lapromt = str_replace("(nivel)", $nivel, $Lapromt);
        $Lapromt = str_replace("(plantillaPracticar)", $plantillaPracticar, $Lapromt);
        return ($Lapromt);
    }

    public static function contarModificarP(&$Lapromt,$materia_nombre = '', $subtopico = '',$Unidad = '', $nivel = '') {
        
        $Lapromt = strtolower($Lapromt);
        $myhelp = new Myhelp();

        $contadorC = 0;
        $contadorP = 0;
        if($nivel === ''){
            $nivel = 'profesional';
        }

        $remplazarPofavo = [
            'Asignatura' => $materia_nombre,
            'asignatura' => $materia_nombre,
            'materia_nombre' => $materia_nombre,
            'materia' => $materia_nombre,
            'Materia' => $materia_nombre,

            'concepto que se enseña' => $Unidad,
            'Concepto que se enseña' => $Unidad,

            'Tema' => $subtopico,
            'tema' => $subtopico,

            'Unidad' => $Unidad,
            'unidad' => $Unidad,

            'nivel' => $nivel,
        ];
        
        foreach ($remplazarPofavo as $key => $value) {
            $keyflexible = strtolower($key);
            $corchetes = "[". $keyflexible."]";
            $parentesis = "(".$keyflexible.")";

            $ArrayCorche = $myhelp->EncontrarEnString($Lapromt,$corchetes);
            $contadorC += count($ArrayCorche);
            $ArrayParent = $myhelp->EncontrarEnString($Lapromt,$parentesis);
            $contadorP += count($ArrayParent);

            if($contadorC !== 0){
                $Lapromt = str_replace($corchetes, strtolower($value), strtolower($Lapromt));
            }
            if($contadorP !== 0){
                $Lapromt = str_replace($parentesis, strtolower($value), strtolower($Lapromt));
            }

        }
        return [ 'corchetes' => $contadorC, 'parentesis' => $contadorP];
    }


    //se usa para resolver ejercicios
    public static function gptPart1($pregunta, $nivel, $materia_nombre, $usuario, &$soloEjercicios, $debug = false) {

        $longuitudPregunta = strlen($pregunta) > 7;

        if ($longuitudPregunta) {
            if (!$debug) {
                $plantillaPracticar = 'Ejercicios para practicar:';
                $client = OpenAI::client(env('GTP_SELECT'));
                $result = $client->completions()->create([
                    'model' => 'text-davinci-003',
                    'prompt' => self::modificarPromt($materia_nombre, $pregunta, $nivel, $plantillaPracticar),
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
                        'tokens_usados' => $restarAlToken,
                        'user_id' => $usuario->id
                    ]);
                    $soloEjercicios = HelpGPT::ApartarSujerencias($respuesta, $plantillaPracticar);
                    return [ 'respuesta' => $respuesta, 'restarAlToken' => $restarAlToken];
                } else {
                    if ($finishingReason == 'length') {
                        return [ 'respuesta' => self::respuestaLarga, 'restarAlToken' => 0];
                    } else {
                        return [ 'respuesta' => self::servicioNOdisponible, 'restarAlToken' => 0];
                    }
                }
            }
            $respuesta = "El ATP es un compuesto importante en nuestro cuerpo ya que es la principal fuente de energía para todos los procesos metabólicos. Es una molécula con una configuración específica hecha de fosfato, nitrógeno y un anillo de purina. Esto significa que hay otras moléculas similares que contienen el mismo tipo de configuración. Algunos ejemplos son ADP (Adenosina Desfosfato), AMP (Adenosina Monofosfato) y NADH (Nico-Adenosina Deshidrogenasa). Estas moléculas similares se usan como fuente de energía para la mayoría de los procesos metabólicos, de modo que se asemejan a la energía proporcionada por el ATP. 
                Ejercicios para practicar: 
                1.¿Qué compuestos se unen para formar el ATP? 
                2. ¿Qué es la nico-adenosina deshidrogenasa? 
                3. ¿Qué rol juega el ATP en nuestro cuerpo?
            ";
            return [ 'respuesta' => $respuesta, 'restarAlToken' => 0];
        }
        return [ 'respuesta' => self::PreguntaCorta, 'restarAlToken' => 0];
    }
    
    public static function gptResolverQuiz(&$elpromp, $subtopico, $nivel, $materia_nombre, $usuario, $debug = false) {
        
        //contarModificarP cambia [tema] = el tema seleccionado
        $corchetesYparentesis = self::contarModificarP($elpromp,$materia_nombre, $subtopico, $nivel);
        //todo: si corchetesYparentesis estan en cero, no debe continuar

        $elpromp.= ". Al final de las opciones, imprime la respuesta correcta por cada pregunta con este formato: RESPUESTA=A";
        $elpromp.= ". Cada opcion, debe ocupar una fila y deben ser 4 opciones de la A a la D";
        $elpromp.= ". La primera fila debe tener un titulo relacionado a lo que se evalua y la segunda fila debe ser la primera pregunta.";
        $elpromp = str_replace("..",".",$elpromp);


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
                    MedidaControl::create([
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
    public static function gptResolverTema(&$elpromp, $subtopico,$unidad, $nivel, $materia_nombre, $usuario, $debug = false) {
        $longuitudPregunta = strlen($subtopico) > 3;
        
        self::contarModificarP($elpromp,$materia_nombre, $subtopico,$unidad, $nivel);
        $YaEstabaGuardada = GrabarGPT::BuscarPromp($elpromp);
        if($YaEstabaGuardada && $YaEstabaGuardada !== ''){
            return [ 'respuesta' => $YaEstabaGuardada, 'restarAlToken' => 0];
        }

        //todo:
        // $numberPermission = Myhelp::getPermissionToNumber();
        // if($numberPermission === 1)
        // return [ 'respuesta' => env('NOTVALIDATEDBYTEACHER'), 'restarAlToken' => 0];


        if ($longuitudPregunta) {
            if (!$debug) {
                $client = OpenAI::client(env('GTP_SELECT'));
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
                    MedidaControl::create([
                        'tokens_usados' => $restarAlToken,
                        'user_id' => $usuario->id
                    ]);


                    //todo: si esprofesor tendra mucho mas peso, que si es alumno
                    //todo: deberia guardar, pero si es profesor, sobreescribe 
                    //todo: pero por ahora, solo guarda cuando es profesor o mas
                    // if($numberPermission === 1)
                    // $respuesta = 'Solo estudiante. '.$respuesta;

                    RespuestaEjercicio::create([
                        'guardar_pregunta' => $elpromp,
                        'respuesta' => $respuesta,
                        'nivel' => $nivel,
                        'precisa' => 3, //todo: 0 (nada preciso) - 5 (muy preciso)
                        'idExistente' => null,
                    ]);

                    return [ 'respuesta' => $respuesta, 'restarAlToken' => $restarAlToken];
                } else {
                    if ($finishingReason == 'length') {
                        return [
                            'respuesta' => self::respuestaLarga, 
                            'restarAlToken' => 0
                        ];
                    } else {
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
            return [
                'respuesta' => $respuesta, 
                'restarAlToken' => 0
            ];
        }
        return [
            'respuesta' => 'El Subtema es demasiado corto', 
            'restarAlToken' => 0
        ];
    }

    public static function CalcularTokenConsumidos($usageRespuesta, $usageRespuestaTotal) {
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

    public static function maxToken() {
        if (config('app.env') === 'production') {
            return 1400; // Adjust the response length as needed
        }
        return 1400;
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
        $result = [['label' => 'Selecciona un promp', 'value' => 'a']];
        foreach ($theArrayofStrings as $key => $value) {
            $result[] = ['label' => $value->principal, 'value' => ($value->id),'tipo' => $value->teoricaOpractica];
        // $ListaPromp = LosPromps::Where('clasificacion','Expectativas Altas')->Where('teoricaOpractica','teorica')->get();
        }
        return $result;
    }

    public static function NEW_turnInSelectID($theArrayofStrings) {
        $result = [['title' => 'Selecciona un promp', 'value' => 0, 'tipo' => 'General']];
        foreach ($theArrayofStrings as $key => $value) {
            $result[] = ['title' => $value->principal, 'value' => ($value->id),'tipo' => $value->teoricaOpractica];
        // $ListaPromp = LosPromps::Where('clasificacion','Expectativas Altas')->Where('teoricaOpractica','teorica')->get();
        }
        return $result;
    }


    //quiz de actionEQH
    public static function gptQuizEstudiante(&$elpromp, $subtopico, $nivel, $materia_nombre, $usuario, $debug = false) {
        
        //contarModificarP cambia [tema] = el tema seleccionado
        $corchetesYparentesis = self::contarModificarP($elpromp,$materia_nombre, $subtopico, $nivel);
        //todo: si corchetesYparentesis estan en cero, no debe continuar

        $elpromp.= ". Al final, imprime la respuesta correcta con este formato: RESPUESTA=A";
        $elpromp.= ". Cada opcion, debe ocupar una fila y deben ser 4 opciones de la A a la D";
        $elpromp.= ". La primera fila debe tener un titulo relacionado a lo que se evalua y la segunda fila debe ser la primera pregunta.";
        $elpromp = str_replace("..",".",$elpromp);

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
                    MedidaControl::create([
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
}
