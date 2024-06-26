<?php

namespace App\helpers;

use App\Http\Controllers\ArticulosController;
use App\Models\Articulo;
use App\Models\Calificacion;
use App\Models\Materia;
use App\Models\MedidaControl;
use App\Models\Parametro;
use DateTime;
use Illuminate\Support\Facades\Auth;
use OpenAI;

class HelpArticulo {
    const respuestaLimite = 'Limite de tokens';
    const respuestaLarga = 'La respuesta es demasiado extensa';


    /**
     * @param $date
     * @return string|null
     */
    public static function updatingDate($date): ?string
    {
        if ($date === null || $date == '1969-12-31') {
            return null;
        }
        return date("Y-m-d", strtotime($date));
    }

    public static function updatingDateTime($dateT) {
        if($dateT === 0) return (new DateTime())->format('Y-m-d H:i:s');

        if ($dateT === null || $dateT == '1969-12-31') {
            return null;
        }
        return (new DateTime(($dateT)))->format('Y-m-d H:i:s');
    }

    /**
     * @param $texto
     * @param $materiaid
     * @param $tipoTexto
     * @param $debug
     *
     * @return array
     */
    public static function MejorarResumen($texto, $materiaid, $tipoTexto, $elFoo, $debug = false): array{
        $usuario = Auth::user();
        ArticulosController::GuardarTiempoUserPrivate($elFoo,'MejorarResumen');

        if($usuario->limite_token_leccion < 1){
            $ChatR = ['respuesta' => 'No hay suficientes tokens ', 'restarAlToken' => 0];
        }else{

            $materia = Materia::find($materiaid);
            $carrera = $materia->carrera()->get()->first();
            if ($debug) { //todo: izzi
                $Instruccion = Parametro::find(2)->prompEjercicios;
                $elpromp =
                    "Teniendo en cuenta que, una asignatura pertenece a una carrera. ".
                    $Instruccion.
                    ", tenga en cuenta que, no debe mencionar la carrera, ni la asignatura".
                    ". El contexto es de la carrera universitaria ". $carrera->nombre. ',' .
                    " de la asignatura: $materia->nombre,".
                    " el texto es un ".$tipoTexto.
                    ". El texto es el siguiente: ". $texto;

                $ChatR = self::davinci($elpromp, $usuario,$materia,'Solicito un Resumen de articulo (el id es de la materia)');
            }else{
                //debugin
                sleep(2);
                $ChatR = ['respuesta' => 'un texto de prueba = MejorarelResumen ', 'restarAlToken' => 0];
            }
        }
        return $ChatR;
    }

    public static function OptimizarResumen($texto, $materiaid,$debug = false) {
        $usuario = Auth::user();
        $materia = Materia::find($materiaid);
        $carrera = $materia->carrera()->get()->first();
        if ($debug) {
            $Instruccion = Parametro::find(2)->prompEjercicios;

            $elpromp = $Instruccion.
                ", teniendo encuenta que el contexto es de la carrera universitaria ". $carrera->nombre.",".
                " Y de la asignatura: $materia->nombre,".
                " el texto es el siguiente: ". $texto.
                ""
            ;

            $ChatR = self::davinci($elpromp, $usuario,$materia,'Solicito un Resumen simple (el id es de la materia)');
            return $ChatR;
            // return ['respuesta' => $ChatR['respuesta'], 'restarAlToken' => $ChatR['restarAlToken']];
        }else{
            //! debugin
            return ['respuesta' => 'un texto de prueba generado por chatGPT', 'restarAlToken' => 0];
        }
    }
    public static function CalificarArticulo($elformulario,$articuloid, $notaManual, $debug = false) {
        $usuario = Auth::user();
        $articulo = Articulo::find($articuloid);
        $materia = Materia::find($articulo->materia_id);
        $notaManual = (float)$notaManual;
        $carrera = $materia->carrera()->get()->first();
        if($notaManual){
            if($notaManual > 5 || $notaManual < 0) {
                return ['respuesta' => 'Nota invalida', 'restarAlToken' => 0];
            }else{
                Calificacion::UpdateOrInsert(
                    [
                        'Modelo_de_libre' => 'articulo_id',
                        'libre_id' => $articuloid,
                        'user_id' => $elformulario['user_id'],
                        'QuienCalifico' => auth()->user()->id
                    ], [
                    'TipoPrueba' => 'Articulo',
//                    'prompUsado' => 'Calificacion Manual',
                    'valor' => $notaManual,
                    'valor_Resumen' => $notaManual,
                    'valor_Introduccion' => $notaManual,
                    'valor_Discusion' => $notaManual,
                    'valor_Conclusiones' => $notaManual,
                    'valor_Metodologia' => $notaManual,
                    'tokens' => 0,
                ]);
                return ['respuesta' => 'Se guardo la nota', 'restarAlToken' => 0];
            }
        }//fin nota manual


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
//            $ChatR =['respuesta' => self::respuestaLarga, 'restarAlToken' => 0];
            $miMismo = new HelpArticulo();
            $valorArticulo = $miMismo->extraerCalificacion($ChatR['respuesta']);

            Calificacion::UpdateOrInsert(
                [
                    'libre_id' => $articuloid,
                    'Modelo_de_libre' => 'articulo_id',
                    'user_id' => $elformulario['user_id'],
                    'QuienCalifico' => auth()->user()->id
                ],[
                'TipoPrueba' => 'Articulo',
                'argumentoIA' => $ChatR['respuesta'],
                'prompUsado' => $elpromp,
                'valorIA' => $valorArticulo,
                'valor_Resumen' => $valorArticulo,
                'valor_Introduccion' => $valorArticulo,
                'valor_Discusion' => $valorArticulo,
                'valor_Conclusiones' => $valorArticulo,
                'valor_Metodologia' => $valorArticulo,
                'tokens' => $ChatR['restarAlToken'],
            ]);

            return $ChatR;
        }
        //debugin
        return ['respuesta' => 'un texto mass largo ome', 'restarAlToken' => 0];
    }


    public static function davinci($elpromp, $usuario,$materia,$Razon):array {
//        $client = OpenAI::client(env('GTP_SELECT'));
//        $result = $client->completions()->create([
//            'model' => 'text-davinci-003',
//            'prompt' => $elpromp,
//            'max_tokens' => HelpGPT::maxToken()
//        ]);
        $result = JustChatFunctionGPT::Chat4($elpromp);
        $respuesta = $result[1];
        $finishingReason = $result[2];
        $restarAlToken = 1;
        if ($finishingReason === 'stop') {
            //TODO: urgente
//            $usageRespuesta = ($result['usage']["completion_tokens"]); //~ 260
//            $usageRespuestaTotal = ($result['usage']["total_tokens"]); //~ 500
//            $restarAlToken = HelpGPT::CalcularTokenConsumidos($usageRespuesta, $usageRespuestaTotal);
            if($usuario){
                $totalNuevo = $usuario->limite_token_leccion - $restarAlToken;
                $totalNuevo = $totalNuevo < 0 ? 0 : $totalNuevo;
                $usuario->update(['limite_token_leccion' => $totalNuevo]);

                MedidaControl::create([
                    'pregunta' => $elpromp,
                    'respuesta_guardada' => $respuesta,
                    'RazonNOSubtopico' => $Razon,
                    'subtopico_id' => $materia->id,
                    'tokens_usados' => $restarAlToken,
                    'user_id' => $usuario->id
                ]);
            }
            return [
                'respuesta' => $respuesta,
                'restarAlToken' => $restarAlToken,
            ];
        }

        if ($finishingReason === 'length') {
            return [
                'respuesta' => [self::respuestaLarga],
                'restarAlToken' => 0,
            ];
        }

        return [
            'respuesta' => ['El servicio no esta disponible'],
            'restarAlToken' => 0,
        ];
    }

    public function ConsultarCalificacion($articuloId){
        $LaCalificacion = Calificacion::Where('TipoPrueba','Articulo')
            ->Where('libre_id',$articuloId)
            ->WhereNotNull('valor')
            ->first();
        $calificacionARticulo = -1;
        if($LaCalificacion){
            $calificacionARticulo = $LaCalificacion->valor;
        }

        $CalificacionIA = Calificacion::Where('TipoPrueba','Articulo')
            ->Where('libre_id',$articuloId)
            ->WhereNotNull('valorIA')
            ->first();
        $calificacionARticuloIA = -1;
        if($CalificacionIA){
            $calificacionARticuloIA = $CalificacionIA->valorIA;
        }
        return [
            'docente' => $calificacionARticulo,
            'IA' => $calificacionARticuloIA,
            'ModelCalificacionIA' => $CalificacionIA
        ];
    }
    public function extraerCalificacion($textoRespuesta){
        $parteCalificacion = strpos(($textoRespuesta), 'Calificación:');
        if ($parteCalificacion !== false) {
            $Calificacion = (substr($textoRespuesta,$parteCalificacion+15,1));
        }
        $Myhelp = new MyHelp();
        if(isset($Calificacion)){
            $calif = $Myhelp->SePuedeConvertirAEntero($Calificacion);
        }else{
            $calif = 0;
        }

        return $calif;
    }


}
