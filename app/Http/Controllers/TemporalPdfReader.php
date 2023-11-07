<?php

namespace App\Http\Controllers;

use App\helpers\GrabarGPT;
use App\helpers\HelpGPT;
use App\helpers\HelpPDF;
use App\helpers\JustChatFunctionGPT;
use App\helpers\Myhelp;
use App\Models\Archivo;
use App\Models\Materia;
use App\Models\MedidaControl;
use App\Models\RespuestaPDf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use OpenAI;
use Smalot\PdfParser\Parser;

class TemporalPdfReader extends Controller
{

    public function Index() {

        return Inertia::render('leyendopdf/Index', [ //carpeta
            'breadcrumbs'       =>  [['label' => __('app.label.leyendopdf'), 'href' => route('leyendopdf')]],
            'title'             =>  'Leyendo PDF',
        ]);
    }
    public function Read(Request $request) {

        $parser = new Parser();
        // $parser = new \Smalot\PdfParser\Parser();
        // $file = Storage::size('app/public/MATRICES.pdf');
        // $file = File::get('/storage/app/public/MATRICES.pdf');
        // dd($file);
        $file = Storage::url('app/public/MATRICES.pdf');
        $file = Storage::path('public/MATRICES.pdf');
        $pdf = $parser->parseFile($file);
        $text = $pdf->getText();

        return Inertia::render('leyendopdf/Index', [ //carpeta
            'breadcrumbs'       =>  [['label' => __('app.label.leyendopdf'), 'href' => route('leyendopdf')]],
            'title'             =>  'Leyendo PDF',
        ]);
    }
    public function verPdf($archivoid) {

        $archivo = Archivo::find($archivoid);
        // $parser = new Parser();
        // $parser = new \Smalot\PdfParser\Parser();
        // $file = Storage::url('app/public/MATRICES.pdf');
        // $file = public_path('storage/archivosSubidos/'.$archivo->NombreOriginal);
        $file = storage_path('app/public/archivosSubidos/'.$archivo->NombreOriginal);
        // $pdf = $parser->parseFile($file);

        $pathToFile = $file;
        // $text = $pdf->getText();

        $headers = ['Content-Type' => 'application/pdf'];
        // pdfview.vue
        return response()->file($pathToFile, $headers);
    }


    public function vistaPDF($archivoid) {
        $parser = new Parser();
        $archivo = Archivo::find($archivoid);

        // $file = Storage::path('public/MATRICES.pdf');
        $file = storage_path('app/public/archivosSubidos/'.$archivo->NombreOriginal);
        // $file = public_path('storage/archivosSubidos/'.$archivo->NombreOriginal);

        $pdf = $parser->parseFile($file);
        $text = $pdf->getText();
        // dd( $text );
        $resumen = substr($text,0,1000);
        return Inertia::render('materia/docs/pdfview', [ //carpeta
            'resumen'           =>  $resumen,
            'archivinid'       =>  $archivoid,
        ]);
    }


    //not ready for using now
    public function subirPDFOpenAI($archivoid) {
        // $parser = new Parser();
        // $archivo = Archivo::find($archivoid);

        // $file = storage_path('app/public/archivosSubidos/'.$archivo->NombreOriginal);

        // $pdf = $parser->parseFile($file);
        // $text = $pdf->getText();
    }


    //not ready for using now
    public function createStream() {
        $client = OpenAI::client(env('GTP_SELECT'));
        $stream = $client->chat()->createStreamed([
            'model' => 'gpt-4',
            'messages' => [
                [
                    'role' => 'user',
                    'content' => 'Dame ejemplos de tecnicas para multiplicar numeros del 1 al 20 entre ellos.'
                ],
            ],
        ]);
        $respuesta = '';
        foreach ($stream as $response) {
            // $delta = $response->delta;
            $choices = $response->choices[0]->toArray();

            $delta = $choices['delta'];
            $finish_reason = $choices['finish_reason'];

            $respuesta2[] = $choices;
            if (isset($delta['content'])) {
            // if (isset($delta['role']) && $delta['role'] === 'assistant') {
                $assistantMessage = $delta['content'];
                $respuesta .= $assistantMessage;
            }
            if ($finish_reason === 'stop') {
                break; // Exit the loop if 'finish_reason' is 'stop'
            }
        }
        dd(
            $respuesta
        );
    }


    /**
     * @param $opcion: entero que se manda desde la URL
     * @param $materia: el objeto materia (Modelo)
     * @param $resumen
     * @return string
     */
    public function OpcionResumenesPDF($opcion, $materia, $resumen): string
    {
        $opciones =[
            'Resume el siguiente texto, asumiendo que eres un experto en la materia '.$materia->nombre . '. el texto es el siguiente: '.$resumen,
            "¿Cuál es el tema central o la principal conclusión del documento que se describe en el siguiente texto? $resumen",
            "¿Cuáles son los puntos clave o los argumentos principales presentados en el siguiente texto? $resumen",
            "¿Quiénes son los autores y cuál es su credibilidad en relación con el tema del siguiente texto? $resumen",
            "¿Cuál es el contexto o la relevancia del contenido del texto en el campo o la industria a la que pertenece?, el texto es el siguiente: $resumen",
        ];

        return $opciones[$opcion];
    }


    /**
     * @param $archivoid
     * @param $opcion
     *
     * @return RedirectResponse|Response
     */
    public function generarResumen($archivoid, $opcion = 0){

        $numberPermissions = Myhelp::getPermissionToNumber(auth()->user()->roles->pluck('name')[0]);

        $archivo = Archivo::find($archivoid);
        $materia = Materia::find($archivo->materia_id);

        $TheUser = Auth()->user();
        $materiasController = new materiasController();
        [$tokensConsumidos, $puedeHacerlo,$text] = $materiasController->AvisarPesoPDF(null,$TheUser,$archivoid);

        if($puedeHacerlo){

            $resumen = substr($text,0,env('MAX_TOKEN_LECTURA_PDF'));
            $promptParaResumir = $this->OpcionResumenesPDF((int)$opcion,$materia,$resumen);
            //# buscando el prompt
            $YaEstabaGuardada = GrabarGPT::BuscarPDFPromt($promptParaResumir);
            if ($YaEstabaGuardada) {
                MedidaControl::create([
                    'pregunta' => $promptParaResumir,
                    'respuesta_guardada' => $YaEstabaGuardada,
                    'tokens_usados' => 0,
                    'user_id' => auth()->user()->id,
                ]);
                $ChatResumen = [0,$YaEstabaGuardada,'Saved'];
            }
            else{
                $Helpgpt = new HelpGPT();
                $tokensPreConsumidos = $Helpgpt->PreCalcularTokenConsumidos($promptParaResumir);
                $tokensUser = auth()->user()->limite_token_leccion;
                if($tokensUser === -1){
                    $ChatResumen = [0,'El PDF no tiene suficiente texto','NotTokens'];
                }else{
                    if($tokensUser < $tokensPreConsumidos){
                        $ChatResumen = [0,'No hay suficientes tokens en su cuenta','NotTokens'];
                    }else{

                        $ChatResumen = JustChatFunctionGPT::Chat4($promptParaResumir);
                        if($ChatResumen[2] === 'stop'){
                            MedidaControl::create([
                                'pregunta' => $promptParaResumir,
                                'respuesta_guardada' => $ChatResumen[1],
                                'tokens_usados' => $ChatResumen[0],
                                'user_id' => auth()->user()->id,
                            ]);
                            $precisa = $numberPermissions == 3 || $numberPermissions > 8 ? 4 :3;
                            RespuestaPDf::create([
                                'guardar_pdf' => $promptParaResumir,
                                'resumen' => $ChatResumen[1],
                                'nivel' => 'universitario',
                                'precisa' => $precisa, //0 (nada preciso) - 5 (muy preciso)
                                'idExistente' => $materia->id.'|materia_id',
                            ]);
                        }else{
                            MedidaControl::create([
                                'pregunta' => $promptParaResumir,
                                'respuesta_guardada' => 'Evento no controlado ChatGPT4',
                                'tokens_usados' => 0,
                                'user_id' => auth()->user()->id,
                            ]);
                        }
                    }
                }
            }

            return Inertia::render('materia/docs/Resumen', [ //carpeta
                'breadcrumbs'               =>  [
                    ['label' => __('app.label.materias'), 'href' => route('materia.index')],
                    ['label' => __('app.label.archivos'), 'href' => route('materia.Archivos',$archivo->materia_id)],
                    ['label' => __('app.label.Resumen'), 'href' => route('generarResumen',$archivo->materia_id)]
                ],
                'numberPermissions'     =>  $numberPermissions,
                'title'                 =>  'Resumen del PDF',
                'ChatResumen'           =>  $ChatResumen,
                'archivinid'            =>  $archivoid,
                'archivo'               =>  $archivo,
                'materia_id'            =>  $archivo->materia_id,
            ]);
        }

        $mensaje2  = 'Este archivo consumirá aproximadamente '. $tokensConsumidos . ' tokens. Usted tiene '.$TheUser->limite_token_leccion . ' restantes.';
        $mensaje = "U -> " . $TheUser->name . " No tiene los tokens para resumir el archivo " . $archivo->nombre;
        DB::commit();
        Log::info($mensaje);
        return Redirect::route('materia.Archivos',$archivo->materia_id)->with('info', $mensaje2);
    }
}
