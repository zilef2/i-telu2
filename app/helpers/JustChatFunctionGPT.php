<?php
namespace App\helpers;

use OpenAI;

class JustChatFunctionGPT {

    public static function Chat($promp){
        $client = OpenAI::client(env('GTP_SELECT'));
        $result = $client->chat()->create([
            "model" => "gpt-4",
            'messages' => [
                ['role' => 'system', 'content' => 'Eres un profesor universitario'],
                ['role' => 'user', 'content' => $promp],
            ],
            'max_tokens' => HelpGPT::maxTokenPDF()
        ]);

        $respuesta = '';
        $finishingReason = 'stop';
        $cuantosIndex = 1;
        foreach ($result->choices as $result) {
            // $result->index; // 0
            $cuantosIndex += $result->index;
            // $result->message->role; // 'assistant'
            $respuesta .= $result->message->content; 

            if($result->finishReason != 'stop') {
                $finishingReason = $result->finishReason;
                break;
            }
        }
        return [$cuantosIndex,$respuesta,$finishingReason];

    }
}
