<?php

namespace App\Jobs;

use App\helpers\HelpGPT;
use App\Models\MedidaControl;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use OpenAI;

class GenerarTituloArticulo implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $elpromp;
    private $usuario;
    private $materia;
    const respuestaLarga = 'La respuesta es demasiado extensa';

    public function __construct($promp,$usuario,$materia)
    {
        $this->elpromp = $promp;
        $this->usuario = $usuario;
        $this->materia = $materia;
    }

    /**
     * Execute the job.
     */
    public function handle():array {
        return [
            'respuesta' => ['El servicio no esta disponible'],
            'restarAlToken' => 0,
        ];
        
        $client = OpenAI::client(env('GTP_SELECT'));
        $result = $client->completions()->create([
            'model' => 'text-davinci-003',
            'prompt' => $this->elpromp,
            'max_tokens' => HelpGPT::maxToken()
        ]);

        $respuesta = $result['choices'][0]["text"];
        $finishReason = $result['choices'][0];
        $finishingReason = $finishReason["finish_reason"] ?? '';

        if ($finishingReason == 'stop') {
            $usageRespuesta = intval($result['usage']["completion_tokens"]); //~ 260
            $usageRespuestaTotal = intval($result['usage']["total_tokens"]); //~ 500
            $restarAlToken = HelpGPT::CalcularTokenConsumidos($usageRespuesta, $usageRespuestaTotal);

            $tokensAntes = intval($this->usuario->limite_token_leccion);
            $this->usuario->update(['limite_token_leccion' => ($tokensAntes) - $restarAlToken]);

            MedidaControl::create([
                'pregunta' => $this->elpromp,
                'respuesta_guardada' => $respuesta,
                'RazonNOSubtopico' => 'Solicitó un Resumen de articulo (el id es de la materia)',
                'subtopico_id' => $this->materia->id,
                'tokens_usados' => $restarAlToken,
                'user_id' => $this->usuario->id
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
}
