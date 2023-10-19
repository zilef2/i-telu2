<?php

namespace App\Jobs;

use App\helpers\GrabarGPT;
use App\helpers\HelpPDF;
use App\helpers\JustChatFunctionGPT;
use App\helpers\Myhelp;
use App\Models\Archivo;
use App\Models\Materia;
use App\Models\MedidaControl;
use App\Models\RespuestaPDf;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Request;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;

class GuardarResumirArchivoPDF implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $req;
    /**
     * Create a new job instance.
     */
    public function __construct(Request $request)
    {
        $this->req = $request;
    }

    /**
     * Execute the job.
     */
    public function handle(): void {
        $theUser = Auth::user();
        $nombreContenido = str_replace(" ","",time() ."_". $this->req->archivo->getClientOriginalName());
        $this->req->archivo->storeAs('public/archivosSubidos',$nombreContenido);

        $archivo = Archivo::create([
            'NombreOriginal'    => $nombreContenido,
            'nombre'            => $this->req->nombre ?? '',
            'peso'              => $this->req->peso ?? 0,
            'type'              => $this->req->type,
            'user_id'           => $theUser->id,
            'materia_id'        => $this->req->materia_id,
        ]);


        //? how to optimizar this part

        // $numberPermissions = Myhelp::getPermissionToNumber(auth()->user()->roles->pluck('name')[0]);

        // $subtopicoid = Materia::find($archivo->materia_id)->subtopico_id;
        // //todo: pedir el subtopico

        // $text = HelpPDF::ParserPDF($archivo);
        // $resumen = substr($text,0,10000);
        // $promptParaResumir = 'Resume el siguiente texto, asumiendo que eres un experto en la materia del que trate el texto:'.$resumen;
        // //# buscando el prompt
        // $YaEstabaGuardada = GrabarGPT::BuscarPDFPromt($promptParaResumir);
        // if ($YaEstabaGuardada) {
        //     MedidaControl::create([
        //         'pregunta' => $promptParaResumir,
        //         'subtopico_id' => $subtopicoid,
        //         'respuesta_guardada' => $YaEstabaGuardada,
        //         'tokens_usados' => 0,
        //         'user_id' => auth()->user()->id,
        //     ]);
        //     $ChatResumen = [0,$YaEstabaGuardada,'Saved'];
        // }else{

        //     $ChatResumen = JustChatFunctionGPT::Chat($promptParaResumir);
        //     if($ChatResumen[2] == 'stop'){
        //         MedidaControl::create([
        //             'pregunta' => $promptParaResumir,
        //             'subtopico_id' => $subtopicoid,
        //             'respuesta_guardada' => $ChatResumen[1],
        //             'tokens_usados' => $ChatResumen[0],
        //             'user_id' => auth()->user()->id,
        //         ]);
        //         $precisa = $numberPermissions == 3 || $numberPermissions > 8 ? 4 :3;
        //         RespuestaPDf::create([
        //             'guardar_pdf' => $promptParaResumir,
        //             'resumen' => $ChatResumen[1],
        //             'nivel' => 'universitario',
        //             'precisa' => $precisa, //0 (nada preciso) - 5 (muy preciso)
        //             'idExistente' => 0,
        //         ]);
        //     }else{
        //         MedidaControl::create([
        //             'pregunta' => $promptParaResumir,
        //             'subtopico_id' => $subtopicoid,
        //             'respuesta_guardada' => 'Evento no controlado ChatGPT4',
        //             'tokens_usados' => 0,
        //             'user_id' => auth()->user()->id,
        //         ]);
        //         // $ChatResumen = [0,'Fallo al resumir','No stop'];
        //     }
        // }
    }
}
