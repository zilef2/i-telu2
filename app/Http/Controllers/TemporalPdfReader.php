<?php

namespace App\Http\Controllers;

use App\helpers\GrabarGPT;
use App\helpers\HelpPDF;
use App\helpers\JustChatFunctionGPT;
use App\helpers\Myhelp;
use App\Models\Archivo;
use App\Models\Materia;
use App\Models\MedidaControl;
use App\Models\RespuestaPDf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
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
        dd( $text );

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



    //esta funcion si se usa correctamenta
    //todo: organizar ideas
    public function generarResumen($archivoid){
        $numberPermissions = Myhelp::getPermissionToNumber(auth()->user()->roles->pluck('name')[0]);

        $archivo = Archivo::find($archivoid);
        $subtopicoid = Materia::find($archivo->materia_id)->subtopico_id;
        //todo: pedir el subtopico

        $text = HelpPDF::ParserPDF($archivoid);
        $resumen = substr($text,0,1000);
        $promptParaResumir = 'Resume el siguiente texto, asumiendo que eres un experto en la materia del que trate el texto:'.$resumen;
        //# buscando el prompt
        $YaEstabaGuardada = GrabarGPT::BuscarPDFPromt($promptParaResumir);
        if ($YaEstabaGuardada) {
            MedidaControl::create([
                'pregunta' => $promptParaResumir,
                'subtopico_id' => $subtopicoid,
                'respuesta_guardada' => $YaEstabaGuardada,
                'tokens_usados' => 0,
                'user_id' => auth()->user()->id,
            ]);
            $ChatResumen = [0,$YaEstabaGuardada,'Saved'];
        }else{

            $ChatResumen = JustChatFunctionGPT::Chat($promptParaResumir);
            if($ChatResumen[2] == 'stop'){
                MedidaControl::create([
                    'pregunta' => $promptParaResumir,
                    'subtopico_id' => $subtopicoid,
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
                    'idExistente' => 0,
                ]);
            }else{
                MedidaControl::create([
                    'pregunta' => $promptParaResumir,
                    'subtopico_id' => $subtopicoid,
                    'respuesta_guardada' => 'Evento no controlado ChatGPT4',
                    'tokens_usados' => 0,
                    'user_id' => auth()->user()->id,
                ]);
                // $ChatResumen = [0,'Fallo al resumir','No stop'];
            }
        }
        // $ChatResumen = [0,'Fallo al resumir','No stop'];
        
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
}
