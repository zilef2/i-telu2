<?php

namespace App\helpers;

use Illuminate\Support\Facades\Http;

class WolframAlphaService
{
    protected $appId;


    public static function query($input)
    {
        $appId = env('WOLFRAM_APP_ID');

        $url = "http://api.wolframalpha.com/v2/query?appid={$appId}&input=" . urlencode($input);

        $response = Http::get($url);

        // Process the response
        $result = $response->json();

        // Return the processed response
        dd($result);
        return $result;
        // Return the processed response
    }
}