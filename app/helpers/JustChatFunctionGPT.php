<?php
namespace App\helpers;

use OpenAI;

class JustChatFunctionGPT {

    public function Chat35turbo0613($promp){
        $client = OpenAI::client(env('GTP_SELECT'));
        $response = $client->chat()->create([
            'model' => 'GPT-3.5-turbo-16k-0613',
            'messages' => [
                ['role' => 'user', 'content' => $promp],
            ],
        ]);

        $resultado = '';
        $finishingReason = 'nobodyknows';
        foreach ($response->choices as $result) {
//            $result->index; // 0
//            $result->message->role; // 'assistant'
            $resultado .= $result->message->content; // '\n\nHello there! How can I assist you today?'
            $finishingReason = $result->finishReason;
            if($finishingReason !== 'stop') {
                $finishingReason = $result->finishReason;
                break;
            }
        }
        return [$response->usage->totalTokens,$resultado,$finishingReason];
    }

    public function Chat35($promp){
        $client = OpenAI::client(env('GTP_SELECT'));
        $response = $client->chat()->create([
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                ['role' => 'user', 'content' => $promp],
            ],
        ]);

        $resultado = '';
        $finishingReason = 'nobodyknows';
        foreach ($response->choices as $result) {
//            $result->index; // 0
//            $result->message->role; // 'assistant'
            $resultado .= $result->message->content; // '\n\nHello there! How can I assist you today?'
            $finishingReason = $result->finishReason;
            if($finishingReason !== 'stop') {
                $finishingReason = $result->finishReason;
                break;
            }
        }
//        $response->toArray(); // ['id' => 'chatcmpl-6pMyfj1HF4QXnfvjtfzvufZSQq6Eq', ...]
        return [$response->usage->totalTokens,$resultado,$finishingReason];
    }

    public static function Chat4($promp){
        ini_set('max_execution_time', 180);
        $client = OpenAI::client(env('GTP_SELECT'));
        $result = $client->chat()->create([
            "model" => "gpt-4",
            'messages' => [
                ['role' => 'system', 'content' => 'Eres una eminencia universitaria'],
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

            if($result->finishReason !== 'stop') {
                $finishingReason = $result->finishReason;
                break;
            }
        }
        return [$cuantosIndex,$respuesta,$finishingReason];
    }
}
