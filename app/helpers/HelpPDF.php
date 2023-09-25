<?php

namespace App\helpers;

use App\Models\Archivo;
// use App\Models\MedidaControl;
// use App\Models\Parametro;
// use App\Models\RespuestaEjercicio;
// use Illuminate\Support\Facades\Auth;
// use Illuminate\Support\Facades\Log;
// use OpenAI;
use Smalot\PdfParser\Parser;

class HelpPDF {

//usado en temporalPdfReader: para procesar un archivo
    public static function ParserPDF($archivoid){
        $parser = new Parser();
        $archivo = Archivo::find($archivoid);

        $file = storage_path('app/public/archivosSubidos/'.$archivo->NombreOriginal);

        $pdf = $parser->parseFile($file);
        return $pdf->getText();
    }

    public static function TextTOJsonl($text){
        // Step 1: Read the plain-text document
        // $text = file_get_contents('plain-text-document.txt');

        // Step 2: Convert to JSONL format
        $lines = explode("\n", $text);

        $jsonlContent = '';
        foreach ($lines as $line) {
            // Assuming each line represents a separate data point
            // You might need to format this line according to your specific use case
            $jsonlContent .= json_encode(['text' => $line]) . "\n";
        }

        // Step 3: Write to a file
        file_put_contents('my-file1.jsonl', $jsonlContent);
    }
}