<?php

namespace App\Jobs;

use App\helpers\HelpArticulo;
use App\helpers\Myhelp;
use App\Models\Articulo;
use App\Models\Materia;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CriticarArticulo implements ShouldQueue{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $Articulo;
    private $userLogueado;
    private $MyHelpBro;

    /**
     * Create a new job instance.
     */
    public function __construct($ArticuloID,$userLogueado){
        $this->Articulo = Articulo::find($ArticuloID);
        $this->$userLogueado = $userLogueado;
        $this->MyHelpBro = new Myhelp();
    }

    /**
     * Execute the job.
     */
    public function handle(): void{
        try {
            $elpromp =
                "Dado el sigueinte articulo titulado: " .
                $this->Articulo->nick .
                ' Haga una critica constructiva al estudiante que escribio el siguiente resumen: ' .
                $this->Articulo->Resumen_final
                . '. No presente mas de 70 palabras.'
                ;
            $materia = Materia::find($this->Articulo->materia_id);
            $ChatR = HelpArticulo::davinci($elpromp, $this->userLogueado , $materia, 'Se califico una critica del resumen');
            $this->Articulo->update([
                'Critica_string' => $ChatR['respuesta']
            ]);
            Myhelp::SoloJobLog($this,'exito en la critica. ArticuloID = '.$this->Articulo->id);
        } catch (\Throwable $th) {
//            $this->warn('Error'. $th->getMessage());
            $problema = ' - ' . $th->getMessage() . ' L:' . $th->getLine() . ' Ubi:' . $th->getFile();

            $this->MyHelpBro::SoloJobLog($this, $problema, true);

            // return ['respuesta' => $ChatR['respuesta'], 'restarAlToken' => $ChatR['restarAlToken']];
            //dd($th->getMessage());
        }
    }
}
//php artisan queue:work database --queue=secondary
