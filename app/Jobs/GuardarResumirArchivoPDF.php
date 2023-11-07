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
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GuardarResumirArchivoPDF implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $archivo,$user;
    /**
     * Create a new job instance.
     */
    public function __construct(Archivo $archivo,$user)
    {
        $this->archivo = $archivo;
        $this->user = $user;
    }

    /**
     * Execute the job.
     */
    public function handle(): void {
        try{
            $Materia = Materia::find($this->archivo->materia_id);

            $text = HelpPDF::ParserPDF($this->archivo);
            $resumen = substr($text,0,env('MAX_TOKEN_LECTURA_PDF'));
            $promptParaResumir = 'Resume el siguiente texto, asumiendo que eres un experto en la materia :'.$Materia->nombre.'. el texto es el siguiente: '.$resumen;
            $justGPT = new JustChatFunctionGPT();
            $ChatResumen = $justGPT->Chat35turbo0613($promptParaResumir);
//            $ChatResumen = $justGPT->Chat35($promptParaResumir);
            (new Myhelp())->EscribirEnLogJobs($this,0, $ChatResumen[1].' - | - ' .$ChatResumen[2]);

             if($ChatResumen[2] === 'stop'){
                 MedidaControl::create([
                     'pregunta' => $promptParaResumir,
                     'respuesta_guardada' => $ChatResumen[1],
                     'tokens_usados' => $ChatResumen[0],
                     'user_id' => $this->user->id,
                 ]);
                 RespuestaPDf::create([
                     'guardar_pdf' => $promptParaResumir,
                     'resumen' => $ChatResumen[1],
                     'nivel' => 'universitario',
                     'precisa' => 3, //0 (nada preciso) - 5 (muy preciso)
                     'idExistente' => 0,
                 ]);

                 $this->archivo->update([
                     'Resumen1' => $ChatResumen[1]
                 ]);
             }else{
                 MedidaControl::create([
                     'pregunta' => $promptParaResumir,
                     'respuesta_guardada' => 'Resumen errado. Evento no controlado ChatGPT4',
                     'tokens_usados' => 0,
                     'user_id' => $this->user->id,
                 ]);
                 // $ChatResumen = [0,'Fallo al resumir','No stop'];
             }
            (new Myhelp())->EscribirEnLogJobs($this,0, 'exito en la el resumen de archivo');

        } catch (\Throwable $th) {
            $problema = $th->getMessage() . ' L:' . $th->getLine() . ' Ubi:' . $th->getFile();
            (new Myhelp())->EscribirEnLogJobs($this,2, $problema);
            $this->fail();
        }
    }
}
