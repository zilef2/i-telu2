<?php

namespace App\helpers;

use App\Jobs\GenerarTituloArticulo;
use App\Models\Calificacion;
use App\Models\Materia;
use App\Models\MedidaControl;
use App\Models\Parametro;
use Illuminate\Support\Facades\Auth;
use OpenAI;

class HelpArticulo {
    const respuestaLimite = 'Limite de tokens';
    const respuestaLarga = 'La respuesta es demasiado extensa';
    const servicioNOdisponible = 'servicioNOdisponible';
    const GPTdesabilitado = 'GPTdesabilitado';
    const PreguntaCorta = 'PreguntaCorta';
    const MAX_USAGE_RESPUESTA = 550;
    const MAX_USAGE_TOTAL = 600;
    const TOKEN_GENERAR_MATERIA = 2000;



    public static function OptimizarResumenOIntroduccion($texto, $materiaid,$tipoTexto,$debug = false) {
        $usuario = Auth::user();
        $materia = Materia::find($materiaid);
        $carrera = $materia->carrera()->get()->first();
        if (!$debug) {
            $Instruccion = Parametro::find(2)->prompEjercicios;
            
            $elpromp =
                $Instruccion.
                ", teniendo encuenta que el contexto es de la carrera universitaria ". $carrera->nombre.",".
                " Y de la asignatura: $materia->nombre,".
                " el texto es un ".$tipoTexto.
                " el texto es el siguiente: ". $texto.
                ""
                ;

            $ChatR = self::davinci($elpromp, $usuario,$materia,'Solicito un Resumen de articulo (el id es de la materia)');
            // $ChatR = dispatch(new GenerarTituloArticulo($elpromp, $usuario,$materia));
            return $ChatR;
            // return ['respuesta' => $ChatR['respuesta'], 'restarAlToken' => $ChatR['restarAlToken']];
        }else{
            //debugin
            return ['respuesta' => 'un texto mass largo ome', 'restarAlToken' => 0];
        }
    }
    public static function CalificarArticulo($elformulario,$articuloid, $notaManual, $debug = false) {
        $usuario = Auth::user();
        $materia = Materia::find($elformulario['materiaid']);
        $carrera = $materia->carrera()->get()->first();
        if($notaManual){
            Calificacion::UpdateOrInsert(
                [
                    'libre_id' => $articuloid,
                    'Modelo_de_libre_id' => 'articulo_id',
                    'user_id' => $elformulario['user_id'],
                    'QuienCalifico' => auth()->user()->id
                ], [
                'TipoPrueba' => 'Articulo',
                'prompUsado' => 'Calificacion Manual',
                'valor' => $notaManual,
                'valor_Resumen' => $notaManual,
                'valor_Introduccion' => $notaManual,
                'valor_Discusion' => $notaManual,
                'valor_Conclusiones' => $notaManual,
                'valor_Metodologia' => $notaManual,
                'tokens' => 0,
            ]);
            return ['respuesta' => 'Se guardo la nota','restarAlToken' => 0];
        }else{

            if (!$debug) {
                $Instruccion = Parametro::find(2)->prompObjetivos;
                
                $elpromp = $Instruccion.
                    "Teniendo encuenta que el contexto es de la carrera universitaria ". $carrera->nombre.",".
                    " Y de la asignatura: $materia->nombre,".
                    " el Resumen del aritculo es el siguiente ".$elformulario['Resumen'][0].
                    ". Tu respuesta siempre debe contener la calificacion con este patron: 'Calificación: (numero 0-5)".
                    ""
                    ;

                $ChatR = self::davinci($elpromp, $usuario,$materia,'Califico un articulo (el id es de la materia)');
                $miMismo = new HelpArticulo();
                $valorArticulo = $miMismo->extraerCalificacion($ChatR['respuesta']);

                Calificacion::UpdateOrInsert(
                    [
                        'libre_id' => $articuloid,
                        'Modelo_de_libre_id' => 'articulo_id',
                        'user_id' => $elformulario['user_id'],
                        'QuienCalifico' => auth()->user()->id
                    ],[
                    'TipoPrueba' => 'Articulo',
                    'prompUsado' => $elpromp,
                    'valor' => $valorArticulo,
                    'valor_Resumen' => $valorArticulo,
                    'valor_Introduccion' => $valorArticulo,
                    'valor_Discusion' => $valorArticulo,
                    'valor_Conclusiones' => $valorArticulo,
                    'valor_Metodologia' => $valorArticulo,
                    'tokens' => $ChatR['restarAlToken'],
                ]);
                
                return $ChatR;
            }else{
                //debugin
                return ['respuesta' => 'un texto mass largo ome', 'restarAlToken' => 0];
            }
        }
    }


    public static function davinci($elpromp, $usuario,$materia,$Razon):array {
        // return [
        //     'respuesta' => [$elpromp.' Respondiendo cualquier cosa temporalmente. mucho texto'],
        //     'restarAlToken' => 0,
        // ];
        
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
            $restarAlToken = HelpGPT::CalcularTokenConsumidos($usageRespuesta, $usageRespuestaTotal);

            $tokensAntes = intval($usuario->limite_token_leccion);
            $usuario->update(['limite_token_leccion' => ($tokensAntes) - $restarAlToken]);

            MedidaControl::create([
                'pregunta' => $elpromp,
                'respuesta_guardada' => $respuesta,
                'RazonNOSubtopico' => $Razon,
                'subtopico_id' => $materia->id,
                'tokens_usados' => $restarAlToken,
                'user_id' => $usuario->id
            ]);

            return [
                'respuesta' => $respuesta,
                'restarAlToken' => $restarAlToken,
            ];
        } else {
            if ($finishingReason == 'length') {
                return [
                    'respuesta' => [self::respuestaLarga],
                    'restarAlToken' => 0,
                ];
            } else {
                return [
                    'respuesta' => ['El servicio no esta disponible'],
                    'restarAlToken' => 0,
                ];
            }
        }

    }

    public function extraerCalificacion($textoRespuesta){

        $parteCalificacion = strpos(($textoRespuesta), 'Calificación:');
        if ($parteCalificacion !== false) {
            $Calificacion = (substr($textoRespuesta,$parteCalificacion+15,1));
        }
        $Myhelp = new MyHelp();
        $calif = $Myhelp->SePuedeConvertirAEntero($Calificacion);

        
        
        return $calif;
    }
}
